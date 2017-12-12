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
* Classe de negócio Variavel
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 6234 $
$Name$
$Author: cassiano $
$Date: 2006-02-14 10:16:30 -0200 (Ter, 14 Fev 2006) $

Casos de uso: uc-01.03.95
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GA_ADM_NEGOCIO."RTipoPrimitivo.class.php"        );
include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoVariavel.class.php"      );
include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoParametro.class.php"     );

/**
    * Classe de regra de negócio Variável
    * Data de Criação: 28/07/2004

    * @author Analista: Ricardo Lopes
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Regra
*/
class RVariavel
{
/**
    * @access Private
    * @var String
*/
var $stNome;
/**
    * @access Private
    * @var Boolean
*/
var $boParametro;
/**
    * @access Private
    * @var Integer
*/
var $inCodVariavel;
/**
    * @access Private
    * @var Integer
*/
var $inCodModulo;

/**
    * @access Private
    * @var Integer
*/
var $inCodBiblioteca;

/**
    * @access Private
    * @var Integer
*/
var $inCodFuncao;
/**
    * @access Private
    * @var String
*/
var $stValorInicial;
/**
    * @access Private
    * @var Integer
*/
var $inOrdem;
/**
    * @access Private
    * @var Object
*/
var $obTVariavel;
/**
    * @access Private
    * @var Object
*/
var $obTParametro;
/**
    * @access Private
    * @var Object
*/
var $obRTipoPrimitivo;
/**
    * @access Private
    * @var Object
*/
var $obTransacao;

/**
    * @access Public
    * @param String $Valor
*/
function setNome($valor) { $this->stNome                = $valor; }
/**
    * @access Public
    * @param Boolean $Valor
*/
function setParametro($valor) { $this->boParametro           = $valor; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setCodVariavel($valor) { $this->inCodVariavel         = $valor; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setCodModulo($valor) { $this->inCodModulo           = $valor; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setCodBiblioteca($valor) { $this->inCodBiblioteca       = $valor; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setCodFuncao($valor) { $this->inCodFuncao           = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setValorInicial($valor) { $this->stValorInicial        = $valor; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setOrdem($valor) { $this->inOrdem               = $valor; }
/**
    * @access Public
    * @param Boolean $Valor
*/
function setTParametro($valor) { $this->obTParametro          = $valor; }
/**
    * @access Public
    * @param Boolean $Valor
*/
function setTVariavel($valor) { $this->obTVariavel           = $valor; }
/**
    * @access Public
    * @param Boolean $Valor
*/
function setRTipoPrimitivo($valor) { $this->obRTipoPrimitivo      = $valor; }

/**
    * @access Public
    * @return String
*/
function getNome() { return $this->stNome                ; }
/**
    * @access Public
    * @return Boolean
*/
function getParametro() { return $this->boParametro           ; }
/**
    * @access Public
    * @return Integer
*/
function getCodVariavel() { return $this->inCodVariavel         ; }
/**
    * @access Public
    * @return Integer
*/
function getCodModulo() { return $this->inCodModulo           ; }
/**
    * @access Public
    * @return Integer
*/
function getCodBiblioteca() { return $this->inCodBiblioteca       ; }
/**
    * @access Public
    * @return Integer
*/
function getCodFuncao() { return $this->inCodFuncao           ; }
/**
    * @access Public
    * @return String
*/
function getValorInicial() { return $this->stValorInicial        ; }
/**
    * @access Public
    * @return Integer
*/
function getOrdem() { return $this->inOrdem               ; }
/**
    * @access Public
    * @return Object
*/
function getTParametro() { return $this->obTParametro          ; }
/**
    * @access Public
    * @return Object
*/
function getTVariavel() { return $this->obTVariavel           ; }
/**
    * @access Public
    * @return Object
*/
function getRTipoPrimitivo() { return $this->obRTipoPrimitivo      ; }

/**
     * Método construtor
     * @access Private
*/
function RVariavel()
{
    $this->setTVariavel      ( new TAdministracaoVariavel      );
    $this->setTParametro     ( new TAdministracaoParametro     );
    $this->setRTipoPrimitivo ( new RTipoPrimitivo   );
    $this->setParametro      ( false                );
    $this->obTransacao = new Transacao;
}

/**
    * Executa um recuperaTodos na classe Persistente de Variavel ou Parametro
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listar(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    if( $this->inCodBiblioteca )
        $stFiltro .= " AND var.cod_biblioteca = ".$this->inCodBiblioteca." ";
    if( $this->inCodModulo >= 0 )
        $stFiltro .= " AND var.cod_modulo = ".$this->inCodModulo." ";
    if( $this->inCodVariavel )
        $stFiltro .= " AND var.cod_variavel = ".$this->inCodVariavel." ";
    if( $this->inCodFuncao )
        $stFiltro .= " AND var.cod_funcao = ".$this->inCodFuncao." ";
    if( $this->inCodTipo )
        $stFiltro .= " AND var.cod_tipo = ".$this->inCodTipo." ";
    if( $this->stNome )
        $stFiltro .= " AND var.nom_variavel = '".$this->stNome."' ";
    $stOrder = ($stOrder)?$stOrder:" ORDER BY nom_variavel ";
    if ($this->boParametro) {
        $stOrder = " ORDER BY ordem ";
        $obErro = $this->obTParametro->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    } else {
        $obErro = $this->obTVariavel->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    }

    return $obErro;
}

/**
    * Executa um recuperaPorChave na classe Persistente Variavel ou Parametro
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultar($boTransacao = "")
{
    $obErro = $this->obTFuncao->recuperaPorChave( $rsRecordSet, $boTransacao );

    if ($this->boParametro) {
        //$obErro = $this->obTParametro->recuperaPorChave( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    } else {
        $this->obTVariavel->setDado("cod_funcao"  , $this->inCodFuncao   );
        $this->obTVariavel->setDado("cod_variavel", $this->inCodVariavel );
        $obErro = $this->obTVariavel->recuperaPorChave( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    }
    if ( !$obErro->ocorreu() ) {
        $this->stNome         = $rsRecordSet->getCampo("nom_variavel");
        $this->stValorInicial = $rsRecordSet->getCampo("valor_inicial");
        $this->obRTipoPrimitivo->setCodTipo( $rsRecordSet->getCampo("cod_tipo") );
        $obErro = $this->obRTipoPrimitivo->consultar();
    }

    return $obErro;
}
/**
    * Salva dados de função externa e corpo da função no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function salvar($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( ($this->obRTipoPrimitivo->getNomeTipo()) && !($this->obRTipoPrimitivo->getCodTipo()) ) {
            $this->obRTipoPrimitivo->listar( $rsTipo, '', $boTransacao);
            $this->obRTipoPrimitivo->setCodTipo( $rsTipo->getCampo('cod_tipo') );
        }
        $this->obTVariavel->setDado( "cod_modulo" , $this->inCodModulo );
        $this->obTVariavel->setDado( "cod_biblioteca" , $this->inCodBiblioteca );
        $this->obTVariavel->setDado( "cod_funcao" , $this->inCodFuncao );
        $this->obTVariavel->proximoCod( $inCodVariavel , $boTransacao );
        $this->setCodVariavel( $inCodVariavel );
        if (!$this->boParametro) {
            $this->obTVariavel->setDado("cod_variavel" , $this->inCodVariavel                   );
            $this->obTVariavel->setDado("cod_tipo"     , $this->obRTipoPrimitivo->getCodTipo()  );
            $this->obTVariavel->setDado("nom_variavel" , $this->stNome                          );
            $this->obTVariavel->setDado("valor_inicial", $this->stValorInicial                  );
            $obErro = $this->obTVariavel->inclusao( $boTransacao );
        } else {
            $this->obTVariavel->setDado("cod_funcao"  , $this->inCodFuncao                     );
            $this->obTVariavel->setDado("cod_variavel", $this->inCodVariavel                   );
            $this->obTVariavel->setDado("cod_tipo"    , $this->obRTipoPrimitivo->getCodTipo()  );
            $this->obTVariavel->setDado("nom_variavel", $this->stNome                          );
            $obErro = $this->obTVariavel->inclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTParametro->setDado("cod_modulo" , $this->inCodModulo );
                $this->obTParametro->setDado("cod_biblioteca" , $this->inCodBiblioteca );
                $this->obTParametro->setDado("cod_funcao"  , $this->inCodFuncao   );
                $this->obTParametro->setDado("cod_variavel", $this->inCodVariavel );
                $this->obTParametro->setDado("cod_tipo", $this->obRTipoPrimitivo->getCodTipo()  );
                $this->obTParametro->setDado("ordem"       , $this->inOrdem       );
                $obErro = $this->obTParametro->inclusao( $boTransacao );
            }
        }

        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTVariavel );
    }

    return $obErro;
}
/**
    * Exclui dados de Variavel e/ou Parametro do banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluir($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        if ($this->boParametro) {
            $stCampoCod  = $this->obTParametro->getCampoCod();
            $stCompChave = $this->obTParametro->getComplementoChave();
            $this->obTParametro->setCampoCod('cod_funcao');
            $this->obTParametro->setComplementoChave('cod_modulo,cod_biblioteca');
            $this->obTParametro->setDado("cod_funcao" , $this->inCodFuncao  );
            $this->obTParametro->setDado("cod_modulo" , $this->inCodModulo );
            $this->obTParametro->setDado("cod_biblioteca" , $this->inCodBiblioteca );
            $obErro = $this->obTParametro->exclusao( $boTransacao );
            $this->obTParametro->setCampoCod        ( $stCampoCod  );
            $this->obTParametro->setComplementoChave( $stCompChave );
        }
        if ( !$obErro->ocorreu() ) {
            $stCampoCod  = $this->obTVariavel->getCampoCod();
            $stCompChave = $this->obTVariavel->getComplementoChave();
            $this->obTVariavel->setCampoCod('cod_funcao');
            $this->obTVariavel->setDado("cod_funcao" , $this->inCodFuncao  );
            $this->obTVariavel->setComplementoChave('cod_modulo,cod_biblioteca');
            $this->obTVariavel->setDado("cod_modulo" , $this->inCodModulo );
            $this->obTVariavel->setDado("cod_biblioteca" , $this->inCodBiblioteca );
            $obErro = $this->obTVariavel->exclusao( $boTransacao );
            $this->obTVariavel->setCampoCod        ( $stCampoCod  );
            $this->obTVariavel->setComplementoChave( $stCompChave );
            $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTVariavel );
        }
    }

    return $obErro;
}

}
