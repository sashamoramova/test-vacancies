export type Vacancy = {
  id: number;
  title: string;
  description: string;
  salary: number;
  created_at: string;
};

export type VacancyList = {
  items: Vacancy[];
  meta: {
    totalCount: number;
    pageCount: number;
    currentPage: number;
    perPage: number;
  };
};

export type ValidationErrors = {
  field: string;
  message: string;
}[];
