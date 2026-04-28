export type Vacancy = {
    id: number
    title: string
    description: string
    salary: number
    created_at: string
    updated_at: string
  }
  
  export type VacancyList = {
    items: Vacancy[]
    meta: {
      totalCount: number
      pageCount: number
      currentPage: number
      perPage: number
    }
  }
  
  export type ValidationErrors = {
    field: string
    message: string
  }[]
  
  export function useApi() {
    const config = useRuntimeConfig()
    const base = process.server ? config.apiBase : config.public.apiBase
  
    return {
      async list(params: {
        sort?: string
        direction?: 'asc' | 'desc'
        page?: number
        perPage?: number
      }): Promise<VacancyList> {
        // Формат Yii2 sort: "field" = ASC, "-field" = DESC
        const sortValue = params.sort
          ? (params.direction === 'desc' ? '-' : '') + params.sort
          : undefined
  
        const query: Record<string, string | number> = {}
        if (sortValue) query.sort = sortValue
        if (params.page) query.page = params.page
        if (params.perPage) query['per-page'] = params.perPage
  
        const res = await $fetch.raw<Vacancy[]>(base + '/vacancies', { query })
        const totalCount = Number(res.headers.get('X-Pagination-Total-Count') || 0)
        const pageCount = Number(res.headers.get('X-Pagination-Page-Count') || 1)
        const currentPage = Number(res.headers.get('X-Pagination-Current-Page') || 1)
        const perPage = Number(res.headers.get('X-Pagination-Per-Page') || 10)
  
        return {
          items: res._data || [],
          meta: { totalCount, pageCount, currentPage, perPage },
        }
      },
  
      async one(id: number | string): Promise<Vacancy> {
        return await $fetch<Vacancy>(`${base}/vacancies/${id}`)
      },
  
      async create(data: {
        title: string
        description: string
        salary: number
      }): Promise<{ model?: Vacancy; errors?: ValidationErrors }> {
        try {
          const model = await $fetch<Vacancy>(base + '/vacancies', {
            method: 'POST',
            body: data,
          })
          return { model }
        } catch (e: any) {
          if (e?.response?.status === 422 && e?.response?._data) {
            return { errors: e.response._data as ValidationErrors }
          }
          throw e
        }
      },
    }
  }
  