# Betcity
Betcity - Клиент системы

**Attributes:**

|  Name  |       Type       |       Description       |
|:------|:------|:-----------------------:|
| Id | ID | Entity ID |
| CreatedAt | string | Entity created at |
| UpdatedAt | string | Entity updated at |
| UserId | int | Внешний ID из контекста Auth |
| Name | string | Имя клиента |
| Gender | string [secret, male, female] | Пол пользователя |

**Commands:**

|  Name  |       Description       |
|:------|:-----------------------:|
| Create | Create command for Client |
| Edit | Update command for Client |
| Remove | Remove command for Client |

**Queries:**

|  Name  |       Description       |
|:------|:-----------------------:|
| Read | Read query for Client |
| Search | Search query for Client |

**Relations:**

|  Entity  |          Type           |
|:------|:-----------------------:|

