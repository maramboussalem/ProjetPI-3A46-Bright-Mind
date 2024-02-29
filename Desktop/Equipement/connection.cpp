#include "connection.h"

#include <QSqlDatabase>
#include <QSqlError>
#include <QSqlQuery>

Connection::Connection()
{

}

bool Connection::createconnect()
{bool test=false;
QSqlDatabase db = QSqlDatabase::addDatabase("QODBC");
db.setDatabaseName("PROJET");
db.setUserName("yass");//inserer nom de l'utilisateur
db.setPassword("123");//inserer mot de passe de cet utilisateur

if (db.open())
test=true;





    return  test;
}
