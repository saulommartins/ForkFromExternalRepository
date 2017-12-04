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
* Classe de negócio ConfiguracaoBanco
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 18697 $
$Name$
$Author: cassiano $
$Date: 2006-12-12 09:09:30 -0200 (Ter, 12 Dez 2006) $

Casos de uso: uc-01.03.96
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GA_ADM_NEGOCIO."RModulo.class.php"                  );

/**
    * Classe de Regra de Negócio Atributo Dinamico
    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria
*/
class RAtributoDinamico
{
/**
    * @var String
    * @access Private
*/
var $stNome;
/**
    * @var String
    * @access Private
*/
var $stValor;
/**
    * @var String
    * @access Private
*/
var $stMascara;
/**
    * @var String
    * @access Private
*/
var $stAjuda;
/**
    * @var String
    * @access Private
*/
var $stRegraCondicional;
/**
    * @var String
    * @access Private
*/
var $stRegraReferencial;
/**
    * @var Integer
    * @access Private
*/
var $inCodTipo;
/**
    * @var Integer
    * @access Private
*/
var $inCodAtributo;
/**
    * @var Integer
    * @access Private
*/
var $inCodCadastro;
/**
    * @var Boolean
    * @access Private
*/
var $boObrigatorio;
/**
    * @var Boolean
    * @access Private
*/
var $boAtivo;
/**
    * @var Boolean
    * @access Private
*/
var $boIndexavel;
/**
    * @var Array
    * @access Private
*/
var $arRegras;
/**
    * @var Array
    * @access Private
*/
var $arValores;
/**
    * @var Object
    * @access Private
*/
var $obRModulo;

/**
    * @access Private
    * @param String $valor
*/
function setNome($valor) { $this->stNome                   = $valor; }
/**
    * @access Private
    * @param String $valor
*/
function setValor($valor) { $this->stValor                  = $valor; }
/**
    * @access Private
    * @param String $valor
*/
function setMascara($valor) { $this->stMascara                = $valor; }
/**
    * @access Private
    * @param String $valor
*/
function setAjuda($valor) { $this->stAjuda                  = $valor; }
/**
    * @access Private
    * @param String $valor
*/
function setRegraCondicional($valor) { $this->stRegraCondicional       = $valor; }
/**
    * @access Private
    * @param String $valor
*/
function setRegraReferencial($valor) { $this->stRegraReferencial       = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setCodTipo($valor) { $this->inCodTipo                = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setCodcadastro($valor) { $this->inCodCadastro            = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setCodAtributo($valor) { $this->inCodAtributo            = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setObrigatorio($valor) { $this->boObrigatorio            = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setAtivo($valor) { $this->boAtivo                  = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setIndexavel($valor) { $this->boIndexavel              = $valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setRegras($valor) { $this->arRegras                 = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRModulo($valor) { $this->obRModulo                = $valor; }

/**
    * @access Public
    * @return String
*/
function getNome() { return $this->stNome;                   }
/**
    * @access Public
    * @return String
*/
function getValor() { return $this->stValor;                  }
/**
    * @access Public
    * @return String
*/
function getMascara() { return $this->stMascara;                }
/**
    * @access Public
    * @return String
*/
function getAjuda() { return $this->stAjuda;                  }
/**
    * @access Public
    * @return String
*/
function getRegraCondicional() { return $this->stRegraCondicional;       }
/**
    * @access Public
    * @return String
*/
function getRegraReferencial() { return $this->stRegraReferencial;       }
/**
    * @access Public
    * @return Integer
*/
function getCodTipo() { return $this->inCodTipo;                }
/**
    * @access Public
    * @return Integer
*/
function getCodCadastro() { return $this->inCodCadastro;            }
/**
    * @access Public
    * @return Integer
*/
function getCodAtributo() { return $this->inCodAtributo;            }
/**
    * @access Public
    * @return Boolean
*/
function getObrigatorio() { return $this->boObrigatorio;            }
/**
    * @access Public
    * @return Boolean
*/
function getAtivo() { return $this->boAtivo;                  }
/**
    * @access Public
    * @return Boolean
*/
function getIndexavel() { return $this->boIndexavel;              }
/**
    * @access Public
    * @return Array
*/
function getRegras() { return $this->arRegras;                 }
/**
    * @access Public
    * @return Array
*/
function getValores() { return $this->arValores;                }
/**
    * @access Public
    * @return Object
*/
function getRModulo() { return $this->obRModulo;                }

/**
    * Método Construtor
    * @access Private
*/
function RAtributoDinamico()
{
    $this->setRModulo             ( new RModulo );
    $this->arValores              = array();
}

/**
    * Método para adicionar Valores Padrões
    * @access Public
    * @param Integer $inCodValor
    * @param String  $stAtivo
    * @param String  $stValor
*/
function addValor($inCodValor , $stAtivo, $stValor = '')
{
    $arValores['cod_valor'] = $inCodValor;
    $arValores['ativo']     = ($stAtivo=='true'||$stAtivo=='t'||$stAtivo=='Sim') ? 'true' : 'false';
    $arValores['valor']     = $stValor;
    array_push( $this->arValores, $arValores );
}

/**
    * Executa Inclusão/Alteração dos Atributos Dinâmicos
    * @access Public
    * @return Object Objeto Erro
*/
function salvar()
{
    $obErro = new Erro;
    $boFlagTransacao = false;
    $obTransacao = new Transacao;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAtributoDinamico.class.php");
        $obTAtributoDinamico = new TAdministracaoAtributoDinamico;
        $obTAtributoDinamico->setDado( 'cod_modulo',   $this->obRModulo->getCodModulo () );
        $obTAtributoDinamico->setDado( 'cod_cadastro', $this->getCodCadastro          () );
        $obTAtributoDinamico->setDado( 'cod_tipo',     $this->getCodTipo              () );
        $obTAtributoDinamico->setDado( 'nao_nulo',     $this->getObrigatorio          () );
        $obTAtributoDinamico->setDado( 'ativo',        $this->getAtivo                () );
        $obTAtributoDinamico->setDado( 'indexavel',    $this->getIndexavel            () );
        $obTAtributoDinamico->setDado( 'nom_atributo', $this->getNome                 () );
        $obTAtributoDinamico->setDado( 'mascara',      $this->getMascara              () );
        $obTAtributoDinamico->setDado( 'ajuda',        $this->getAjuda                () );
        //VERIFICA SE É INCLUSÃO OU ALTERAÇÃO
        if ( $this->getCodAtributo() ) {
            $obErro = $this->varificarNome( $this->getCodAtributo(), $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $obTAtributoDinamico->setDado( 'cod_atributo', $this->getCodAtributo()  );
                $obErro = $obTAtributoDinamico->alteracao( $boTransacao );
            }
        } else {
            $obErro = $this->varificarNome( '', $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $obTAtributoDinamico->proximoCod ( $inCod,         $boTransacao );
                $obTAtributoDinamico->setDado    ( 'cod_atributo', $inCod       );
                $obErro = $obTAtributoDinamico->inclusao( $boTransacao );
            }
        }
        //Salva os valores padrões
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->salvaValores( $obTAtributoDinamico->getDado('cod_atributo'), $boTransacao);
        }
        //Salva as regras de integridade
        if ( !$obErro->ocorreu() && !$this->getCodAtributo() ) {
            include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAtributoIntegridade.class.php");
            $obTAtribIntegridade = new TAdministracaoAtributoIntegridade;
            $stRegraCondicional = $this->geraRegraCondicional( $obTAtributoDinamico->getDado('cod_atributo')  );
            $obTAtribIntegridade->setDado('cod_atributo'      , $obTAtributoDinamico->getDado('cod_atributo') );
            $obTAtribIntegridade->setDado('cod_modulo'        , $this->obRModulo->getCodModulo()              );
            $obTAtribIntegridade->setDado('cod_cadastro'      , $this->getCodCadastro()                       );
            $obTAtribIntegridade->setDado('cod_integridade'   , 1                                             );
            $obTAtribIntegridade->setDado('regra'             , $stRegraCondicional                           );
            $obErro = $obTAtribIntegridade->inclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $stRegraReferencial = $this->geraRegraReferencial();
                $obTAtribIntegridade->setDado('cod_atributo'   , $obTAtributoDinamico->getDado('cod_atributo') );
                $obTAtribIntegridade->setDado('cod_modulo'     , $this->obRModulo->getCodModulo()              );
                $obTAtribIntegridade->setDado('cod_cadastro'   , $this->getCodCadastro()                       );
                $obTAtribIntegridade->setDado('cod_integridade', 2                                             );
                $obTAtribIntegridade->setDado('regra'          , $stRegraReferencial                           );
                $obErro = $obTAtribIntegridade->inclusao( $boTransacao );
            }
        }
        if ($inCod && !$this->getCodAtributo()) {
            $this->inCodAtributo = $inCod;
        }
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTAtributoDinamico );

    return $obErro;
}

/**
    * Verifica se já existe outro atributo cadastrado com o nome informado para o cadastro selecionado
    * @access Public
    * @return Object Objeto Erro
*/
function varificarNome($inCodigoAtributo, $boTransacao = "")
{
    include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAtributoDinamico.class.php");
    $obTAtributoDinamico = new TAdministracaoAtributoDinamico;
    $stFiltro  = " WHERE                                                    \n";
    $stFiltro .= "     COD_MODULO = ".$this->obRModulo->getCodModulo()."AND \n";
    $stFiltro .= "     COD_CADASTRO = ".$this->getCodCadastro()." AND       \n";
    $stFiltro .= "     UPPER( NOM_ATRIBUTO ) =           ";
    $stFiltro .= "     UPPER( '".$this->getNome()."' )  \n";
    if ($inCodigoAtributo) {
        $stFiltro .= " AND COD_ATRIBUTO != ".$inCodigoAtributo." \n";
    }
    $obErro = $obTAtributoDinamico->recuperaTodos( $rsAtributos , $stFiltro, '', $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( !$rsAtributos->eof() ) {
            $obErro->setDescricao( 'O nome '.$this->getNome().' já foi informado para o cadastro selecionado!' );
        }
    }

    return $obErro;
}

/**
    * Executa Exclusão dos Atributos Dinâmicos
    * @access Public
    * @param  Integer $inCodAtributo
    * @return Object Objeto Erro
*/
function excluir($boTransacao = "")
{
    $obErro = new Erro;
    $boFlagTransacao = false;
    $obTransacao = new Transacao;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAtributoValorPadrao.class.php");
        $obTAtributoValorPadrao = new TAdministracaoAtributoValorPadrao;
        $stCampoCod = $obTAtributoValorPadrao->getCampoCod();
        $obTAtributoValorPadrao->setCampoCod('');
        $obTAtributoValorPadrao->setDado ( 'cod_modulo',   $this->obRModulo->getCodModulo () );
        $obTAtributoValorPadrao->setDado ( 'cod_cadastro', $this->getCodCadastro          () );
        $obTAtributoValorPadrao->setDado ('cod_atributo' , $this->getCodAtributo          () );
        $obErro = $obTAtributoValorPadrao->exclusao( $boTransacao );
        $obTAtributoValorPadrao->setCampoCod( $stCampoCod );
        if ( !$obErro->ocorreu() ) {
            include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAtributoIntegridade.class.php");
            $obTAtribIntegridade = new TAdministracaoAtributoIntegridade;
            $stCampoCod = $obTAtribIntegridade->getCampoCod();
            $obTAtribIntegridade->setCampoCod( "" );
            $obTAtribIntegridade->setDado ( 'cod_modulo',   $this->obRModulo->getCodModulo () );
            $obTAtribIntegridade->setDado ( 'cod_cadastro', $this->getCodCadastro          () );
            $obTAtribIntegridade->setDado ( 'cod_atributo', $this->getCodAtributo          () );
            $obErro = $obTAtribIntegridade->exclusao( $boTransacao );
            $obTAtribIntegridade->setCampoCod( $stCampoCod );
            if ( !$obErro->ocorreu() ) {
                include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAtributoDinamico.class.php");
                $obTAtributoDinamico = new TAdministracaoAtributoDinamico;
                $obTAtributoDinamico->setDado ( 'cod_modulo',   $this->obRModulo->getCodModulo () );
                $obTAtributoDinamico->setDado ( 'cod_cadastro', $this->getCodCadastro          () );
                $obTAtributoDinamico->setDado ( "cod_atributo", $this->getCodAtributo          () );
                $obErro = $obTAtributoDinamico->exclusao( $boTransacao );
                if ($obErro->ocorreu()) {
                    if ( strpos($obErro->getDescricao(),"fk_") ) {
                        $obErro->setDescricao( "O atributo não pode ser excluído porque está sendo utilizado." );
                    }
                }
            }
        }
    } else {
        $obErro->setDescricao( "Erro abrindo a transação!" );
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro , $obTAtributoDinamico );

    return $obErro;
}

function salvaValores($inCodAtributo, $boTransacao = '')
{
    $obErro = new Erro;
    $arValores = $this->getValores();
    $obErro->setDescricao('Algum valor deve estar ativo');
    foreach ($arValores as $arValor) {
        if ($arValor['ativo'] == 'true') {
            $obErro->setDescricao('');
            break;
        }
    }
    
    if ( !$obErro->ocorreu() ) {
        foreach ($arValores as $arValor) {
            include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAtributoValorPadrao.class.php");
            //ajuste do campo de valor padrao para insercao corretamente no banco 
            $stObservacao = str_replace("\r\n", "\n", $arValor['valor']);
            $obTAtributoValorPadrao = new TAdministracaoAtributoValorPadrao;
            $obTAtributoValorPadrao->setDado('cod_atributo'   , $inCodAtributo);
            $obTAtributoValorPadrao->setDado('cod_modulo'     , $this->obRModulo->getCodModulo() );
            $obTAtributoValorPadrao->setDado('cod_cadastro'   , $this->getCodCadastro() );
            $obTAtributoValorPadrao->setDado('cod_valor'      , $arValor['cod_valor']);
            $obTAtributoValorPadrao->setDado('ativo'          , $arValor['ativo']);
            $obTAtributoValorPadrao->setDado('valor_padrao'   , $stObservacao);
            $obErro = $obTAtributoValorPadrao->recuperaPorChave($rsRecordSet, $boTransacao);
            
            if ( !$obErro->ocorreu() ) {
                if ( $rsRecordSet->eof() ) {
                    $obErro = $obTAtributoValorPadrao->inclusao ( $boTransacao );                    
                } else {
                    $obErro = $obTAtributoValorPadrao->alteracao( $boTransacao );
                }
            }
            if( $obErro->ocorreu() )
                break;
        }
    }

    return $obErro;
}

/**
    * Gera Regra Condicional a partir do atributo informado
    * @access Public
    * @param  Integer $inCodAtributo
    * @return String String contendo a regra condicional
*/
function geraRegraCondicional($inCodAtributo)
{
    global $_POST;
    $stRegraCondicional = $inCodAtributo;
    if ($_POST['stOperacaoCondicional'] and $_POST['inTxtValorAtributo']) {
        $stRegraCondicional .= ",".$_POST['stOperacaoCondicional']."_".$_POST['inTxtValorAtributo'];
    } else {
        $stRegraCondicional .= ",";
    }
    if ($_POST['inCodAtributoDinamico'] and $_POST['stOperacaoCondicional'] and $_POST['inTxtValorAtributoValida']) {
        $stRegraCondicional .= ",".$_POST['inCodAtributoDinamico']."_".$_POST['stOperacaoCondicional']."_".$_POST['inTxtValorAtributoValida'];
    } else {
        $stRegraCondicional .= ",";
    }

    return $stRegraCondicional;
}

/**
    * Gera Regra Condicional a partir do atributo informado
    * @access Public
    * @return String String contendo o comando SQL para efetuar a validação
*/
function geraRegraReferencial()
{
    global $_POST;
    $arCampos = explode(",", $_POST['stCampoReferencial'] );
    $stWhere = "";
    foreach ($arCampos as $inIndice => $stCampo) {
            $stWhere .= $stCampo." = VLR_VALIDA".$inIndice." AND ";
    }
    $stWhere = substr( $stWhere, 0, strlen( $stWhere ) - 4 );
    $stSql = " SELECT ".$_POST['stCampoReferencial']." FROM ".$_POST['stTabelaReferencial']." WHERE ".$stWhere;

    return $stSql;
}

/**
    * Verifica Integridade Condicional em relação aos parâmetros informados
    * @access Public
    * @param  String $stAtributo
    * @param  Integer $inCodAtributo
    * @param  Integer $inCodTipo
    * @param  Object $boTransacao
    * @return Object Objeto erro
*/
function verificaIntegridadeCondicional($stAtributo, $inCodAtributo, $inCodTipo="", $boTransacao = "")
{
    $obErro = new Erro;

    return $obErro;
}

/**
    * Verifica Integridade Referencial em relação aos parâmetros informados
    * @access Public
    * @param  String $stAtributo
    * @param  Integer $inCodAtributo
    * @param  Integer $inCodTipo
    * @param  Object $boTransacao
    * @return Object Objeto erro
*/function verificaIntegridadeReferencial($stAtributo, $inCodAtributo, $inCodTipo = "", $boTransacao = "") {
    $obErro = new Erro;
    $stWhere  = " WHERE COD_ATRIBUTO = ".$inCodAtributo." AND ";
    $stWhere .= " COD_INTEGRIDADE = 2 AND ";
    if($inCodTipo)
        $stWhere .= " COD_TIPO = ".$inCodTipo." ";
    include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAtributoIntegridade.class.php");
    $obTAtribIntegridade = new TAdministracaoAtributoIntegridade;
    $obTAtribIntegridade->recuperaTodos( $rsAtribIntegridade, $stWhere );
    $stRegra = $rsAtribRestIntegridade->getCampo('regra');
    $arAtributo = preg_split( "/[^a-zA-Z0-9]/",  $stAtributo );
    foreach ($arAtributo as $inIndice => $stAtributo) {
        $stRegra = str_replace( $stAtributo, "VLR_VALIDA".$inIndice, $stRegra);
    }
    $obErro = $obTAtribIntegridade->recuperaRegra( $rsIntRef, $stRegra, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( $rsIntRef->eof() ) {
            $obErro->setDescricao( "Erro de integridade referencial!" );
        }
    }

    return $obErro;
}

/**
    * Verifica Integridade em relação aos parâmetros informados
    * @access Public
    * @param  String $stAtributo
    * @param  Integer $inCodAtributo
    * @param  Integer $inCodTipo
    * @param  Object $boTransacao
    * @return Object Objeto erro
*/
function verificaIntegridade($stAtributo, $inCodAtributo, $inCodTipo = "", $boTransacao = "")
{
    $obErro = new Erro;
    $obErro = $this->verificaIntegridadeCondicional( $stAtributo, $inCodAtributo, $inCodTipo, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $obErro = $this->verificaIntegridadeReferencial( $stAtributo, $inCodAtributo, $inCodTipo, $boTransacao );
    }

    return $obErro;
}

/**
    * Efetua consulta nos atributos.
    * @access Public
    * @param  Integer $inCodAtributo
    * @param  Integer $inCodTipo
    * @return Object Objeto erro
*/
function consultar(&$rsAtributo, $boTransacao = "")
{
    include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAtributoValorPadrao.class.php");
    $stFiltro  = " WHERE cod_atributo =".$this->getCodAtributo();
    $stFiltro .= " AND cod_cadastro = ".$this->getCodCadastro();
    $stFiltro .= " AND cod_modulo = ".$this->obRModulo->getCodModulo();
    $obTAtributoValorPadrao = new TAdministracaoAtributoValorPadrao;
    $obErro = $obTAtributoValorPadrao->recuperaTodos($rsValorPadrao, $stFiltro , "ORDER BY cod_valor", $boTransacao);
    while ( !$rsValorPadrao->eof() ) {
        $rsValorPadrao->getCampo('cod_valor');
        $rsValorPadrao->getCampo('ativo');
        $rsValorPadrao->getCampo('valor_padrao');
        $this->addValor ( $rsValorPadrao->getCampo('cod_valor')
                                        ,$rsValorPadrao->getCampo('ativo')
                                        ,$rsValorPadrao->getCampo('valor_padrao') );
        $rsValorPadrao->proximo();
    }
    if ( !$obErro->ocorreu() ) {
        $stFiltro  = " AND ad.cod_modulo   = ".$this->obRModulo->getCodModulo()." \n";
        $stFiltro .= " AND ad.cod_cadastro = ".$this->getCodCadastro()." \n";
        $stFiltro .= " AND ad.cod_atributo = ".$this->getCodAtributo()." \n";
        include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAtributoDinamico.class.php" );
        $obTAtributoDinamico = new TAdministracaoAtributoDinamico;
        $obErro = $obTAtributoDinamico->recuperaRelacionamento( $rsAtributo,$stFiltro,'', $boTransacao);
        if ( !$obErro->ocorreu() ) {
            $this->setNome                    ( $rsAtributo->getCampo( "nom_atributo" ) );
            $this->setMascara                 ( $rsAtributo->getCampo( "mascara"      ) );
            $this->setCodTipo                 ( $rsAtributo->getCampo( "cod_tipo"     ) );
            $this->setObrigatorio             ( $rsAtributo->getCampo( "nao_nulo"     ) );
            $this->setAtivo                   ( $rsAtributo->getCampo( "ativo"        ) );
            $this->obRModulo->setCodModulo    ( $rsAtributo->getCampo( "cod_modulo"   ) );
            $this->setAjuda                   ( $rsAtributo->getCampo( "ajuda"        ) );
            $stFiltro = '';
            $stOrder = " ORDER BY COD_INTEGRIDADE ";
            include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAtributoIntegridade.class.php" );
            $obTAtribIntegridade = new TAdministracaoAtributoIntegridade;
            $obErro = $obTAtribIntegridade->recuperaTodos( $rsAtributoIntegridade ,$stFiltro ,$stOrder, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $arRegraCondicional =  array();
                while ( !$rsAtributoIntegridade->eof() ) {
                    if ( $rsAtributoIntegridade->getCampo( "cod_integridade" ) == 1 ) {
                        $arAtributo = explode(",",$rsAtributoIntegridade->getCampo("regra") );
                        $stChaveAtrbutoCIM = $arAtributo[0];
                        $arRegraCondicionalTMP = explode("_", $arAtributo[1] );
                        $arRegraCondicional[0] = array( "sinal" => trim( $arRegraCondicionalTMP[0] ) , "valor" => trim( $arRegraCondicionalTMP[1] ) );
                        $arRegraCondicionalTMP = explode("_", $arAtributo[2] );
                        $arRegraCondicional[1] = array( "chave" => trim( $arRegraCondicionalTMP[0] ), "sinal" => trim( $arRegraCondicionalTMP[1] ), "valor" => trim( $arRegraCondicionalTMP[2] ) );
                    } elseif ( $rsAtributoIntegridade->getCampo( "cod_integridade" ) == 2 ) {
                        $stRegra = $rsAtributoIntegridade->getCampo("regra");
                        $stCampo = substr( $stRegra , 8, strpos( $stRegra, " FROM") - 8);
                        $inPosInicial = strpos( $stRegra, " FROM ") + 6;
                        $inPosFinal = strpos( $stRegra, " WHERE ") - $inPosInicial;
                        $stTabela = substr( $stRegra , $inPosInicial, $inPosFinal );
                        $arRegraCondicional[2] = array( "campo" => trim( $stCampo ), "tabela" => trim( $stTabela ) );
                    }
                    $rsAtributoIntegridade->proximo();
                }
                $this->setRegras( $arRegraCondicional );
            }
        }
    }

    return $obErro;
}

/**
    * Efetua um recuperaTodos na classe de mapeamento TTipoAtributo
    * @access Public
    * @param  Object $rsTipoAtributo Objeto de saida do tipo RecordSet
    * @param  Object $boTransacao
    * @return Object Objeto erro
*/
function recuperaTodosTipoAtributo(&$rsTipoAtributo, $obTransacao = "")
{
    include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoTipoAtributo.class.php" );
    $obTTipoAtributo = new TAdministracaoTipoAtributo;
    $obErro = $obTTipoAtributo->recuperaTodos( $rsTipoAtributo, "", " ORDER BY cod_tipo ", $obTransacao );

    return $obErro;
}
/**
    * Efetua um recuperaPorChave na classe de mapeamento TTipoAtributo
    * @access Public
    * @param  Object $rsTipoAtributo Objeto de saida do tipo RecordSet
    * @param  Object $boTransacao
    * @return Object Objeto erro
*/
function consultarTipoAtributo(&$rsTipoAtributo, $obTransacao = "")
{
    include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoTipoAtributo.class.php" );
    $obTTipoAtributo = new TAdministracaoTipoAtributo;
    $obTTipoAtributo->setDado("cod_tipo", $this->getCodTipo() );
    $obErro = $obTTipoAtributo->recuperaPorChave( $rsTipoAtributo, $obTransacao );

    return $obErro;
}

/**
    * Efetua um recuperaTodos na classe de mapeamento TAtributo
    * @access Public
    * @param  Object $rsAtributoDinamico Objeto de saida do tipo RecordSet
    * @param  String $stOrder
    * @param  Object $boTransacao
    * @return Object Objeto erro
*/
function listar(&$rsAtributoDinamico, $stOrder="" ,$obTransacao = "")
{
    if( $this->obRModulo->getCodModulo() )
        $stFiltro .= ' AND AD.cod_modulo='.$this->obRModulo->getCodModulo();
    if ( $this->getCodCadastro() ) {
        $stFiltro .= " AND AD.cod_cadastro = ".$this->getCodCadastro();
    }
    include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAtributoDinamico.class.php" );
    $obTAtributoDinamico = new TAdministracaoAtributoDinamico;
    $stOrder = ($stOrder) ? $stOrder : " ORDER BY nom_atributo";
    $obErro = $obTAtributoDinamico->recuperaRelacionamento( $rsAtributoDinamico, $stFiltro, $stOrder, $obTransacao );

    return $obErro;
}

}
