# RealCraft
数据库结构

表名 user
  	名字	类型	排序规则	属性	空	默认	额外	操作
  	
	1	id	int(10)		UNSIGNED ZEROFILL	否	无	AUTO_INCREMENT （主键）

	2	username	varchar(20)	latin1_swedish_ci		否	无		

	3	password	char(32)	latin1_swedish_ci		否	无		

	4	email	varchar(50)	latin1_swedish_ci		否	无

 	5 	wood

 	6 	stone

	7 	food
	
表名 construction

//incomplete

 1 id #主键
 
 2 playerId #与user中的id对应
 
 3 location #e.g ['x':12, 'y':12]
 
 4 value

 5 workforce

 表名 resourceLocation
 //incomplete

 1 id 

 2 location

 3 wood

 4 stone

 5 food
 
 6 workforce
