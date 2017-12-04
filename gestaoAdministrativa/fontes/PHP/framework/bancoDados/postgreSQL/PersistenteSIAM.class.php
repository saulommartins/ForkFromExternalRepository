<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
* Classe de Persistência com banco de dados
* Data de Criação: 05/02/2004

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Desenvolvedor: Diego Barbosa Victoria

* @package bancoDados
* @subpackage postgreSQL

Casos de uso: uc-01.01.00

*/

/**
    * Classe de persistênsia que executa as querys mais comuns dinamicamente no banco de dados
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
    * @author Desenvolvedor: Diego Barbosa Victoria
*/
class PersistenteSIAM extends Objeto
{
/**
    * @var String
    * @access Private
*/
var $stTabela;
/**
    * @var String
    * @access Private
*/
var $stCampoCod;
/**
    * @var String
    * @access Private
*/
var $stSequence;
/**
    * @var String
    * @access Private
*/
var $stComplementoChave;
/**
    * @var Array
    * @access Private
*/
var $arEstrutura;
/**
    * @var Array
    * @access Private
*/
var $arEstruturaAuxiliar;
/**
    * @var Object
    * @access Private
*/
//var $obAuditoria;

/**
    * @access Public
    * @param String $valor
*/
function setTabela($valor) { $this->stTabela           = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setCampoCod($valor) { $this->stCampoCod         = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setSequence($valor) { $this->stSequence         = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setComplementoChave($valor) { $this->stComplementoChave = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
//function setAuditoria($valor) { $this->obAuditoria        = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
//function setDescAuditoria($valor) { $this->obAuditoria->setDado('objeto',$valor);}
/**
    * @access Public
    * @param Array $valor
*/
function setEstrutura($valor) { $this->arEstrutura        = $valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setEstruturaAuxiliar($valor) { $this->arEstruturaAuxiliar= $valor; }

/**
    * @access Public
    * @return String
*/
function getTabela() { return $this->stTabela;                        }
/**
    * @access Public
    * @return String
*/
function getCampoCod() { return $this->stCampoCod;                      }
/**
    * @access Public
    * @return String
*/
function getSequence() { return $this->stSequence;                      }
/**
    * @access Public
    * @return String
*/
function getComplementoChave() { return $this->stComplementoChave;              }
/**
    * @access Public
    * @return Object
*/
//function getAuditoria() { return $this->obAuditoria;                     }
/**
    * @access Public
    * @return Object
*/
//function getDescAuditoria() { return $this->obAuditoria->getDado('objeto');  }
/**
    * @access Public
    * @return Array
*/
function GetEstrutura() { return $this->arEstrutura;                     }
/**
    * @access Public
    * @return Array
*/
function GetEstruturaAuxiliar() { return $this->arEstruturaAuxiliar;     }

/**
    * Método Construtor
    * @access Private
*/
function Persistente()
{
    parent::Objeto();
//    $this->setAuditoria        ( new TAuditoria() );
    $this->setEstrutura        ( array() );
    $this->setEstruturaAuxiliar( array() );
}

/**
    * Adiciona campos na estrutura da Persistente.
    * Cada vez que este método é chamado é adicionado um novo campo na estrutura.
    * @access Public
    * @param String  $stNome
    * @param String  $stTipo
    * @param Boolean $boRequerido
    * @param Numeric $nrTamanho
    * @param Boolean $boPrimaryKey
    * @param Boolean $boForeignKey
    * @param String  $stConteudo
*/
function AddCampo($stNome,$stTipo,$boRequerido='', $nrTamanho='',$boPrimaryKey='',$boForeignKey='',$stConteudo = '')
{
    $obCampo = new CampoTabela;
    $obCampo->setNomeCampo ($stNome);
    $obCampo->setTipoCampo ($stTipo);
    $obCampo->setTamanho   ($nrTamanho);
    $obCampo->setRequerido ($boRequerido);
    $obCampo->setPrimaryKey($boPrimaryKey);
    $obCampo->setForeignKey($boForeignKey);
    $obCampo->setConteudo  ($stConteudo);
    if( $stTipo == "AUXILIAR")
        array_push($this->arEstruturaAuxiliar,$obCampo);
    else
        array_push($this->arEstrutura,$obCampo);
}

/**
    * Seta o dado indicado pelo parâmetro $stNomeCampo com o valor $stValor
    * Desta maneira, é carregado o objeto Persistente com o determinado valor.
    * @access Public
    * @param String  $stNomeCampo
    * @param Mixed   $stValor
*/
function setDado($stNomeCampo , $stValor)
{
    $inCont = 0;
    foreach ($this->getEstrutura() as $obCampo) {
        if ($stNomeCampo == $obCampo->getNomeCampo()) {
            $this->arEstrutura[$inCont]->setConteudo($stValor);

            return true;
        }
        $inCont++;
    }
    $inCont = 0;
    foreach ($this->getEstruturaAuxiliar() as $obCampo) {
        if ($stNomeCampo == $obCampo->getNomeCampo()) {
            $this->arEstruturaAuxiliar[$inCont]->setConteudo($stValor);

            return true;
        }
        $inCont++;
    }
    //Caso não tenha encontrado o campo na Estrutura nem na EstruturaAuxiliar, adiciona o campo na Estrutura Auxiliar
    $this->AddCampo($stNomeCampo,'AUXILIAR','','','','',$stValor);
}
/**
    * Recupera o dado indicado pelo parâmetro $stNomeCampo
    * Desta maneira, é retornado do objeto Persistente o determinado valor.
    * @access Public
    * @param  String $stNomeCampo
    * @return Mixed  $stValor
*/
function getDado($stNomeCampo)
{
    $inCont = 0;
    foreach ($this->getEstrutura() as $obCampo) {
        if ($stNomeCampo == $obCampo->getNomeCampo()) {
            return $obCampo->getConteudo();
        }
        $inCont++;
    }
    $inCont = 0;
    foreach ($this->getEstruturaAuxiliar() as $obCampo) {
        if ($stNomeCampo == $obCampo->getNomeCampo()) {
            return $obCampo->getConteudo();
        }
        $inCont++;
    }
}

/**
    * Seta os dados necessários à auditoria e efetua a inclusão no banco de dados.
    * @access Private
    * @param  Boolean $boTransacao
*/
function montaAuditoria($boTransacao)
{
    if (strtolower(get_class($this)) != 'tauditoria') {
        $stValor = $this->obAuditoria->getDado('objeto');
        for ($inCount=0; $inCount<strlen($stValor); $inCount++) {
            if ($stValor[ $inCount ] == '[') $inInicial = $inCount;
            if (($stValor[ $inCount ] == ']') && ($inInicial)) {
                $stOut .= $this->getDado( trim( substr($stValor,$inInicial+1,(($inCount-$inInicial)-1)) ) );
                $inInicial = false;
            }elseif(!$inInicial)
                $stOut .= $stValor[ $inCount ];
        }
        $this->obAuditoria->setDado('objeto',$stOut);

        $arMicroTime = explode(" ", microtime() );
        $stData = date("Y-m-d H:m:", $arMicroTime[1] );//2004-03-03 09:44:10.22
        $inMinuto = date("s", $arMicroTime[1] );
        $stMinutos = $inMinuto + $arMicroTime[0];
        if ($inMinuto < 10) {
            $stMinutos = "0".$stMinutos;
        }
        $stTimeStamp = $stData.substr($stMinutos, 0, 6 );
        $this->obAuditoria->setDado('timestamp', $stTimeStamp );
        $this->obAuditoria->inclusao( $boTransacao );
    }
}
/**
    * Efetua a inclusão no banco de dados a partir do comando DML montado no método montaInclusao.
    * @access Public
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function inclusao($boTransacao = "")
{
    $obErro     = new Erro;
    $obConexao  = new ConexaoSIAM( $boTransacao );
    $obErro = $obConexao->buscaParametros( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->setDebug( 'inclusao' );

        if ( $this->validaInclusao( $boTransacao ) ) {
            $stSql = $this->montaInclusao();
            $obErro = $obConexao->executaDML( $stSql, $boTransacao );
        } else {
            $obErro->setDescricao( "Erro na inclusão do registro!<br>\n" );
        }
    } else {
        $obErro->setDescricao( "Erro ao conectar o banco de dados!\n" );
    }

    return $obErro;
}
/**
    * Efetua a exclusão no banco de dados a partir do comando DML montado no método montaExclusao.
    * @access Public
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function exclusao($boTransacao = "")
{
    $obErro     = new Erro;
    $obConexao  = new ConexaoSIAM( $boTransacao );
    $this->setDebug( 'exclusao' );
    $obErro = $obConexao->buscaParametros( $boTransacao );
    if ( !$obErro->ocorreu() ) {

        if ( $this->validaExclusao( $boTransacao ) ) {
            $stSql = $this->montaExclusao();;
            $stChave = $this->montaChave();
            if ($stChave) {
                $stSql .= " WHERE ".$stChave;
                $obErro = $obConexao->executaDML( $stSql, $boTransacao );
            } else {
                $obErro->setDescricao( "Na classe persistente deve ser setada a chave!<br>\n" );
            }
        } else {
            $obErro->setDescricao( "Erro na exclusão do registro!<br>\n" );
        }
    } else {
        $obErro->setDescricao( "Erro ao conectar o banco de dados!\n" );
    }

    return $obErro;
}
/**
    * Efetua a alteração de registros no banco de dados a partir do comando DML montado no método montaAlteracao.
    * @access Public
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function alteracao($boTransacao = "")
{
    $obErro     = new Erro;
    $obConexao  = new ConexaoSIAM( $boTransacao );
    $this->setDebug( 'alteracao' );
    $obErro = $obConexao->buscaParametros( $boTransacao );
    if ( !$obErro->ocorreu() ) {

        if ( $this->validaAlteracao( $boTransacao ) ) {
            $stSql = $this->montaAlteracao();
            $stChave = $this->montaChave();
            if ($stChave) {
                $stSql .= " WHERE ".$stChave;
                $obErro = $obConexao->executaDML( $stSql, $boTransacao );
                //if ( !$obErro->ocorreu() ) {
                    //
                //}v
                //if( (Sessao::read('numCgm')) && (Sessao::read('acao')) )
                //    $this->montaAuditoria( $boTransacao );
            } else {
                $obErro->setDescricao( "Na classe persistente deve ser setada a chave!<br>\n" );
            }
        } else {
            $obErro->setDescricao( "Erro na alteração do registro!<br>\n" );
        }
    } else {
        $obErro->setDescricao( "Erro ao conectar o banco de dados!\n" );
    }

    return $obErro;
}
/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaTodos.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaTodos(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new ConexaoSIAM( $boTransacao );
    $rsRecordSet = new RecordSet;
    $obErro = $obConexao->buscaParametros( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaTodos().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    } else {
        $obErro->setDescricao( "Erro ao conectar o banco de dados!\n" );
    }

    return $obErro;
}
/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaChave.
    * Este método é responsável por criar os filtros a partir dos valores informados na Persistente da tabela específica.
    * OS métodos disponíveis para filtro são: setCampoCod e setComplementoChave.
    * @access Public
    * @param  Integer $rsRecordSet Objeto RecordSet
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaPorChave(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new ConexaoSIAM( $boTransacao );
    $rsRecordSet = new RecordSet;
    $obErro = $obConexao->buscaParametros( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $stSql = $this->montaRecuperaPorChave();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    } else {
        $obErro->setDescricao( "Erro ao conectar o banco de dados!\n" );
    }

    return $obErro;
}
/**
    * Mesma função do recuperaTodos, mas efetua chamada a outro método para montar o SQL, o método montaRecuperaRelacionamento.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaRelacionamento(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new ConexaoSIAM( $boTransacao );
    $rsRecordSet = new RecordSet;
    $obErro = $obConexao->buscaParametros( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaRelacionamento().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    } else {
        $obErro->setDescricao( "Erro ao conectar o banco de dados!\n" );
    }

    return $obErro;
}
/**
    * Recupera o próximo código da tabela, a partir do atributo stCampoCod setado na Persistente da tabela espefícica.
    * @access Public
    * @param  Integer $inCod Parâmetro de saída contendo o máximo valor do código incrementado em 1
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function proximoCod(&$inCod, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new ConexaoSIAM( $boTransacao );
    $rsRecordSet = new RecordSet;
    $obErro = $obConexao->buscaParametros( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $stComplemento = $this->montaComplementoChave();
        if ($stComplemento) {
            $stComplemento = " WHERE ".$stComplemento;
        }
        $stSql = "SELECT MAX(".$this->getCampoCod().") AS CODIGO FROM ".$this->getTabela().$stComplemento;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $inCod = $rsRecordSet->getCampo("codigo") + 1;
        }
    } else {
        $obErro->setDescricao( "Erro ao conectar o banco de dados!\n" );
    }

    return $obErro;
}
/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaMaxTimestamp.
    * @access Public
    * @param  Integer $rsRecordSet Objeto RecordSet
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaMaxTimestamp(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new ConexaoSIAM( $boTransacao );
    $rsRecordSet = new RecordSet;
    $obErro = $obConexao->buscaParametros( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $stSql = $this->montaRecuperaMaxTimestamp();
        $stChave = $this->montaChave();
        if ($stChave) {
            $stSql .= " WHERE ".$stChave;
        }
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    } else {
        $obErro->setDescricao( "Erro ao conectar o banco de dados!\n" );
    }

    return $obErro;
}

/**
    * Monta o SQL contendo o complemento de chave da tabela.
    * Cada tipo de campo recebe aqui um tratamento diferenciado.
    * @access Public
    * @return String String contendo a parte condicional do SQL
*/
function montaComplementoChave()
{
    $stComplementoChave = str_replace ( " ", "", $this->getComplementoChave() );
    $stRetorno = "";
    if ($stComplementoChave) {
        $arComplementoChave = explode(",", $stComplementoChave);
        foreach ($arComplementoChave as $stCampo) {
            if ($stCampo) {
                $inValor = $this->getDado($stCampo);
                if ($inValor != "") {
                    foreach ($this->GetEstrutura() as $obCampo) {
                        if ( $obCampo->GetNomeCampo() == $stCampo ) {
                            switch ($obCampo->GetTipoCampo()) {
                                case("date"):
                                    $stRetorno .= $stCampo." = TO_DATE('".$inValor."','dd/mm/yyyy') AND ";
                                break;
                                case("timestamp"):
                                    $stRetorno .= $stCampo." = TO_TIMESTAMP('".$inValor."','yyyy-mm-dd hh24:mi:ss.us') AND ";
                                break;
                                case("timestamp_date"):
                                    $stRetorno .= $stCampo." = TO_TIMESTAMP('".$inValor."','dd/mm/yyyy') AND ";
                                break;
                                case("data_hora"):
                                    $stRetorno .= $stCampo." = TO_DATE(''".$inValor."',,'dd/mm/yyyy') AND ";
                                break;
                                case("char"):
                                case("text"):
                                case("varchar"):
                                    $stRetorno .= $stCampo." = '".$inValor."' AND ";
                                break;
                                case("numeric"):
                                    $nrValor = str_replace('.', '', $obCampo->GetConteudo() );
                                    $nrValor = str_replace(',', '.', $nrValor );
                                    $stSql.= "\n    '".$nrValor."',";
                                break;
                                default:
                                    $stRetorno .= $stCampo." = ".$inValor." AND ";
                                break;
                            }
                        }
                    }
                }

            } else {
                $stRetorno .= $stCampo." IS NULL AND ";
            }
        }
        $stRetorno = substr( $stRetorno, 0, strlen( $stRetorno ) - 4 );
    }

    return $stRetorno;
}
/**
    * Monta o SQL contendo a chave principal da tabela.
    * Cada tipo de campo recebe aqui um tratamento diferenciado.
    * @access Public
    * @return String String contendo a parte condicional do SQL
*/
function montaChave()
{
    $stChave = $this->getCampoCod();
    if ($stChave) {
        $inValor = $this->getDado($stChave);
        if ($inValor != "") {
            foreach ($this->GetEstrutura() as $obCampo) {
                if ( $obCampo->GetNomeCampo() == $stChave ) {
                    switch ($obCampo->GetTipoCampo()) {
                        case("date"):
                            $stChave = $stChave." = TO_DATE('".$inValor."','dd/mm/yyyy') ";
                        break;
                        case("timestamp"):
                            $stChave = $stChave." = TO_TIMESTAMP('".$inValor."','yyyy-mm-dd hh24:mi:ss.us') ";
                        break;
                        case("timestamp_date"):
                            $stChave = $stChave." = TO_TIMESTAMP('".$inValor."','dd/mm/yyyy') ";
                        break;
                        case("data_hora"):
                            $stChave = $stChave." = TO_DATE(''".$inValor."',,'dd/mm/yyyy') ";
                        break;
                        case("char"):
                        case("text"):
                        case("varchar"):
                            $stChave = $stChave." = '".$inValor."' ";
                        break;
                        case("numeric"):
                            $nrValor = str_replace('.', '', $obCampo->GetConteudo() );
                            $nrValor = str_replace(',', '.', $nrValor );
                            $stSql.= "\n    '".$nrValor."',";
                        break;
                        default:
                            $stChave = $stChave." = ".$inValor." ";
                        break;
                    }
                }
            }
        } else {
            $stChave = $stChave." IS NULL ";
        }
    }
    $stComplemento = $this->montaComplementoChave();
    if ($stComplemento) {
        if( $stChave )
            $stChave = $stChave." AND ".$stComplemento;
        else
            $stChave = $stComplemento;
    }

    return $stChave;
}
/**
    * Monta o SQL a ser chamado no método RecuperaPorChave.
    * Recupera parte do SQL montado pelo método montaRecuperaTodos e outra pelo montaChave.
    * @access Public
    * @return String String contendo o SQL
*/
function montaRecuperaPorChave()
{
    $stSql  = $this->montaRecuperaTodos();
    $stChave = $this->montaChave();
    if ($stChave) {
        $stSql .= " WHERE ".$stChave;
    }

    return $stSql;
}
/**
    * Monta a cláusula SQL inicial (até o FROM table)
    * Cada tipo de campo recebe aqui um tratamento diferenciado em seu retorno.
    * @access Public
    * @return String String contendo o SQL
*/
function montaRecuperaTodos()
{
    $stSql = "SELECT ";
    foreach ($this->getEstrutura() as $obCampo) {
        switch ($obCampo->getTipoCampo()) {
            case("date"):
                $stSql.= "\n    TO_CHAR(".$obCampo->getNomeCampo().",'dd/mm/yyyy') AS ".$obCampo->getNomeCampo()." ,";
                break;
            case("timestamp"):
                $stSql.= "\n    TO_CHAR(".$obCampo->getNomeCampo().",'yyyy-mm-dd hh24:mi:ss.us') AS ".$obCampo->getNomeCampo()." ,";
                break;
            case("timestamp_date"):
                $stSql.= "\n    TO_CHAR(".$obCampo->getNomeCampo().",'dd/mm/yyyy') AS ".$obCampo->getNomeCampo()." ,";
                break;
            case("data_hora"):
                $stSql.= "\n    TO_CHAR(".$obCampo->getNomeCampo().",'dd/mm/yyyy hh24:mi:ss') AS ".$obCampo->getNomeCampo()." ,";
                break;
            case("number"):
                $stSql.= "\n    TO_CHAR(".$obCampo->getNomeCampo().",'99D999D999G99') AS ".$obCampo->getNomeCampo()." ,";
                break;
            default:
                $stSql.= "\n    ".$obCampo->getNomeCampo()." ,";
                break;
        }
    }
    $stSql = substr($stSql,0,strlen($stSql) - 1);
    $stSql.= "\nFROM ";
    $stSql.= "\n    ".$this->getTabela();

    return $stSql;
}
/**
    * Monta a cláusula DML contendo os campos e valores necessários para efetuar o INSERT
    * Cada tipo de campo recebe aqui um tratamento diferenciado na inserção.
    * @access Public
    * @return String String contendo o DML INSERT
*/
function MontaInclusao()
{
    $stSql = "INSERT INTO ".$this->GetTabela()." (";
    foreach ($this->GetEstrutura() as $obCampo) {
        if ($obCampo->GetRequerido() or $obCampo->GetConteudo() or $obCampo->GetConteudo()=='0') {
        //if ($obCampo->GetRequerido() or $obCampo->GetConteudo()) {
            $stSql.= "\n    ".$obCampo->GetNomeCampo().",";
        }
    }
    $stSql = substr($stSql,0,strlen($stSql) - 1);
    $stSql.= "\n)VALUES(";
    foreach ($this->GetEstrutura() as $obCampo) {
        if ($obCampo->GetRequerido() or $obCampo->GetConteudo() or $obCampo->GetConteudo()=='0') {
        //if ($obCampo->GetRequerido() or $obCampo->GetConteudo()) {
            switch ($obCampo->GetTipoCampo()) {
                case("date"):
                    //Verifica se foi passado algum conteúdo na variável
                    //Caso não tenha sido passado é inserido null no campo para que o
                    //registro não fique com a data 01-01-0000 BC
                    if($obCampo->GetConteudo() == "" || $obCampo->GetConteudo() == null)
                        $stSql.= "\n    null,";
                    else
                        $stSql.= "\n    TO_DATE('".$obCampo->GetConteudo()."' ,'dd/mm/yyyy'),";
                break;
                case("data_hora"):
                    $stSql.= "\n    TO_DATE('".$obCampo->GetConteudo()."' ,'dd/mm/yyyy hh24:mi:ss'),";
                break;
                case("time"):
                    $stSql.= "\n    TO_TIMESTAMP('".$obCampo->GetConteudo()."' ,'hh24:mi'),";
                break;
                case("timestamp"):
                    $stSql.= "\n    TO_TIMESTAMP('".$obCampo->GetConteudo()."' ,'yyyy-mm-dd hh24:mi:ss.us'),";
                break;
                case("timestamp_date"):
                    $stSql.= "\n    TO_TIMESTAMP('".$obCampo->GetConteudo()."' ,'dd/mm/yyyy'),";
                break;
                case("char"):
                case("text"):
                case("varchar"):
                    $stSql.= "\n    '".$obCampo->GetConteudo()."',";
                break;
                case("numeric"):
                    $nrValor = $obCampo->GetConteudo();
                    if (strstr($nrValor,",")) {
                        $nrValor = str_replace('.', '', $nrValor );
                        $nrValor = str_replace(',', '.', $nrValor );
                    }
//                  $stSql.= "\n    '".$nrValor."',";
                    $stSql.= "\n    ".$nrValor.",";
                break;
                case("boolean"):
                    if( $obCampo->GetConteudo() == "true" || $obCampo->GetConteudo() == "t")
                        $stValor = "true";
                    elseif( $obCampo->GetConteudo() == "false" || $obCampo->GetConteudo() == "f" || $obCampo->GetConteudo() == false)
                        $stValor = "false";
                    $stSql.= "\n    '".$stValor."',";
                break;
                default:
                    $stSql.= "\n    ".$obCampo->GetConteudo()." ,";
                break;
            }
        }
    }
    $stSql = substr($stSql,0,strlen($stSql) - 1)."\n)";

    return $stSql;
}
/**
    * Monta a cláusula DML contendo os campos e valores necessários para efetuar o UPDATE
    * Cada tipo de campo recebe aqui um tratamento diferenciado na alteração.
    * @access Public
    * @return String String contendo o DML UPDATE
*/
function MontaAlteracao()
{
    $stSql = "UPDATE ".$this->GetTabela()." SET ";
    foreach ($this->GetEstrutura() as $obCampo) {
        if ($obCampo->GetRequerido() or $obCampo->GetConteudo() or $obCampo->GetConteudo()=='0') {
        //if ($obCampo->GetRequerido() or $obCampo->GetConteudo()) {
            switch ($obCampo->GetTipoCampo()) {
                case("date"):
                    $stSql.= "    ".$obCampo->GetNomeCampo()."=";
                    //Verifica se foi passado algum conteúdo na variável
                    //Caso não tenha sido passado é inserido null no campo para que o
                    //registro não fique com a data 01-01-0000 BC
                    if($obCampo->GetConteudo() == "" || $obCampo->GetConteudo() == null)
                        $stSql.= "null,";
                    else
                        $stSql.= "TO_DATE('".$obCampo->GetConteudo()."' ,'dd/mm/yyyy'),";
                break;
                case("data_hora"):
                    $stSql.= "    ".$obCampo->GetNomeCampo()."=";
                    $stSql.= "TO_DATE('".$obCampo->GetConteudo()."' ,'dd/mm/yyyy hh24:mi:ss'),";
                break;
                case("time"):
                    $stSql.= "\n    TO_TIMESTAMP('".$obCampo->GetConteudo()."' ,'hh24:mi'),";
                break;
                case("timestamp"):
                    $stSql.= "    ".$obCampo->GetNomeCampo()."=";
                    $stSql.= "TO_TIMESTAMP('".$obCampo->GetConteudo()."' ,'yyyy-mm-dd hh24:mi:ss.us'),";
                break;
                case("timestamp_date"):
                    $stSql.= "    ".$obCampo->GetNomeCampo()."=";
                    $stSql.= "TO_TIMESTAMP('".$obCampo->GetConteudo()."' ,'dd/mm/yyyy'),";
                break;
                case("char"):
                case("text"):
                case("varchar"):
                    $stSql.= "    ".$obCampo->GetNomeCampo()."=";
                    $stSql.= " '".$obCampo->GetConteudo()."',";
                break;
                case("numeric"):
                    $stSql.= "    ".$obCampo->GetNomeCampo()."=";
                    //$nrValor = str_replace('.', '', $obCampo->GetConteudo() );
                    //$nrValor = str_replace(',', '.', $nrValor );
                    $nrValor = $obCampo->GetConteudo();
                    if (strstr($nrValor,",")) {
                        $nrValor = str_replace('.', '', $nrValor );
                        $nrValor = str_replace(',', '.', $nrValor );
                    }
                    $stSql.= "\n    '".$nrValor."',";
                break;
                case("boolean"):
                    $stSql.= "    ".$obCampo->GetNomeCampo()."=";
                    if( $obCampo->GetConteudo() == "true" || $obCampo->GetConteudo() == "t")
                        $stValor = "true";
                    elseif( $obCampo->GetConteudo() == "false" || $obCampo->GetConteudo() == "f" || $obCampo->GetConteudo() == false)
                         $stValor = "false";
                    $stSql.= "\n    '".$stValor."',";
                break;
                default:
                    $stSql.= "    ".$obCampo->GetNomeCampo()."=";
                    $stSql.= " ".$obCampo->GetConteudo().",";
                break;
            }
        }
    }
    $stSql= substr($stSql,0,strlen($stSql) - 1);

    return $stSql;
}
/**
    * Monta a cláusula DML contendo os campos e valores necessários para efetuar o DELETE
    * @access Public
    * @return String String contendo o DML DELETE
*/
function montaExclusao()
{
    $stSql = " DELETE FROM ".$this->getTabela();

    return $stSql;
}
/**
    * Método pré-definido pela classe Persistente.
    * Caso haja necessidade de utilização em uma classe estendida, basta efetuar uma sobreposição de métodos
    * @access Public
    * @return String String vazia
*/
function montaRecuperaRelacionamento()
{
    return "";
}
/**
    * Monta a cláusula SQL contendo o MAX(timestamp) responsável por pegar o último timestamp da tabela.
    * @access Public
    * @return String String vazia
*/
function montaRecuperaMaxTimestamp()
{
    $stSql = "SELECT MAX(timestamp) as timestamp FROM ".$this->getTabela();

    return $stSql;
}

/**
    * Método pré-definido pela classe Persistente, responsável por efetuar validações da inclusão.
    * Caso haja necessidade de utilização em uma classe estendida, basta efetuar uma sobreposição de métodos
    * @access Public
    * @param  Boolean $boTransacao
    * @return Boolean true
*/
function validaInclusao($boTransacao = "") { return true; }
/**
    * Método pré-definido pela classe Persistente, responsável por efetuar validações da exclusão.
    * Caso haja necessidade de utilização em uma classe estendida, basta efetuar uma sobreposição de métodos
    * @access Public
    * @param  Boolean $boTransacao
    * @return Boolean true
*/
function validaExclusao($boTransacao = "") { return true; }
/**
    * Método pré-definido pela classe Persistente, responsável por efetuar validações da alteração.
    * Caso haja necessidade de utilização em uma classe estendida, basta efetuar uma sobreposição de métodos
    * @access Public
    * @param  Boolean $boTransacao
    * @return Boolean true
*/
function validaAlteracao($boTransacao = "") { return true; }

}
