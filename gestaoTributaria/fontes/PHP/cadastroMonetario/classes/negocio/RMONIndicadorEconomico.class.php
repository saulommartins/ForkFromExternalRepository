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
    * Classe de regra de negocio para MONETARIO.INDICADOR_ECONOMICO
    * Data de Criacao: 19/12/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho

    * @package URBEM
    * @subpackage Regra

    * $Id: RMONIndicadorEconomico.class.php 60940 2014-11-25 18:03:14Z michel $

* Casos de uso: uc-05.05.07
*/

/*
$Log$
Revision 1.8  2006/09/15 14:46:22  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

/*include_once ( "../../../includes/Constante.inc.php"        );*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_TRANSACAO      );
include_once ( CAM_GT_MON_MAPEAMENTO."TMONIndicadorEconomico.class.php" );
include_once ( CAM_GT_MON_MAPEAMENTO."TMONFormulaIndicador.class.php" );
include_once ( CAM_GT_MON_MAPEAMENTO."TMONValorIndicador.class.php" );

class RMONIndicadorEconomico
{
/**
    * @access Private
    * @var Array
*/
var $arDados;
/**
    * @access Private
    * @var Integer
*/
var $inCodIndicador;
/**
    * @access Private
    * @var Integer
*/
var $inPrecisao;
/**
    * @access Private
    * @var Float
*/
var $inValor;
/**
    * @access Private
    * @var String
*/
var $stDescricao;
/**
    * @access Private
    * @var String
*/
var $stAbreviatura;
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
    * @var Date
*/
var $dtVigencia;
var $dtVigenciaAntes;
/**
    * @access Private
    * @var String
*/
var $strFormula;
/**
    * @access Private
    * @var Object
*/
var $obTMONIndicador;
/**
    * @access Private
    * @var Object
*/
var $obTMONFormulaIndicador;
/**
    * @access Private
    * @var Object
*/
var $obTMONValorIndicador;
var $obErro;
var $inAcaoAlteraValor;

//SETTERS
function setCodIndicador($valor) { $this->inCodIndicador = $valor; }
function setDescricao($valor) { $this->stDescricao = $valor; }
function setAbreviatura($valor) { $this->stAbreviatura = $valor; }
function setPrecisao($valor) { $this->inPrecisao = $valor; }
function setCodFuncao($valor) { $this->inCodFuncao = $valor; }
function setCodModulo($valor) { $this->inCodModulo = $valor; }
function setCodBiblioteca($valor) { $this->inCodBiblioteca = $valor; }
function setDtVigencia($valor) { $this->dtVigencia = $valor; }
function setDtVigenciaAntes($valor) { $this->dtVigenciaAntes = $valor; }
function setValor($valor) { $this->inValor = $valor; }
function setDados($valor) { $this->arDados = $valor; }
function setStrFormula($valor) { $this->strFormula = $valor; }

//GETTERS
function getCodIndicador() { return $this->inCodIndicador; }
function getDescricao() { return $this->stDescricao; }
function getAbreviatura() { return $this->stAbreviatura; }
function getPrecisao() { return $this->inPrecisao; }
function getCodFuncao() { return $this->inCodFuncao; }
function getCodModulo() { return $this->inCodModulo; }
function getCodBiblioteca() { return $this->inCodBiblioteca; }
function getDtVigencia() { return $this->dtVigencia; }
function getDtVigenciaAntes() { return $this->dtVigenciaAntes; }
function getValor() { return $this->inValor; }
function getDados() { return $this->arDados; }
function getStrFormula() { return $this->StrFormula; }

/**
* Metodo construtor
* @access Private
*/
function RMONIndicadorEconomico()
{
    $this->obTransacao      = new Transacao;
    $this->obErro = new Erro;
    // instancia mapeamentos
    $this->obTMONIndicador  = new TMONIndicadorEconomico;
    $this->obTMONFormulaIndicador = new TMONFormulaIndicador;
    $this->obTMONValorIndicador = new TMONValorIndicador;
}

/**
    * Inclui os dados referentes ao Indicador
    * @access Public
    * @param  Object $obTransacao Parametro Transacao
    * @return Object Objeto Erro
*/
function IncluirIndicador($boTransacao = "")
{
//INCLUSAO NA TABELA INDICADOR ECONOMICO
$boFlagTransacao = false;
$obErro = $this->verificaIndicador();
if ( !$obErro->ocorreu() ) {

    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->obTMONIndicador->proximoCod( $this->inCodIndicador, $boTransacao );
        if ( !$obErro->ocorreu() ) {

            $x = $this->getCodIndicador();
            $this->obTMONIndicador->setDado("cod_indicador", $x);
            $this->obTMONIndicador->setDado("descricao",$this->getDescricao() );
            $this->obTMONIndicador->setDado("abreviatura" ,$this->getAbreviatura() );
            $this->obTMONIndicador->setDado("precisao" ,$this->getPrecisao () );
            $obErro = $this->obTMONIndicador->inclusao( $boTransacao );

            //Comentado Regra de Conversão, pois a mesma não vai mais existir na ação, conforme ticket #22008
            /*
            if ( !$obErro->ocorreu() ) {
                //tabela formula_acrescimo
                $this->obTMONFormulaIndicador->setDado("cod_indicador", $x);
                $this->obTMONFormulaIndicador->setDado("cod_funcao", $this->getCodFuncao());
                $this->obTMONFormulaIndicador->setDado("cod_modulo", $this->getCodModulo());
                $this->obTMONFormulaIndicador->setDado("cod_biblioteca", $this->getCodBiblioteca());
                $this->obTMONFormulaIndicador->setDado("inicio_vigencia", $this->getDtVigencia ());
                $obErro = $this->obTMONFormulaIndicador->inclusao( $boTransacao);
           }
           */
        }//fim primeira inseraco
    }//fim verirfica Indicador
  $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTMONIndicador );
}

  return $obErro;
}

/**
    * INCLUIR dados referentes ao valor indicador INDICADOR
    * @access Public
    * @param  Object $obTransacao Parametro Transacao
    * @return Object Objeto Erro
*/
function IncluirValorIndicador($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTMONValorIndicador->setDado("cod_indicador", $this->getCodIndicador());
        $obErro = $this->obTMONValorIndicador->exclusao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $inTotal = count($this->arDados);
            for ($inX=0; $inX<$inTotal; $inX++) {
                $this->obTMONValorIndicador->setDado("inicio_vigencia", $this->arDados[$inX]["dtVigencia"] );
                $this->obTMONValorIndicador->setDado("valor", $this->arDados[$inX]["inValor"] );
                $obErro = $this->obTMONValorIndicador->inclusao( $boTransacao );
                if ( $obErro->ocorreu() ) {
                    break;
                }
            }
        }
   }

   $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTMONValorIndicador );

 return $obErro;
}

/**
    * Altera os dados referentes ao INDICADOR
    * @access Public
    * @param  Object $obTransacao Parametro Transacao
    * @return Object Objeto Erro
*/
function AlterarIndicador($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {

        $this->obTMONIndicador->setDado("cod_indicador", $this->getCodIndicador());
        $this->obTMONIndicador->setDado("descricao", $this->getDescricao());
        $this->obTMONIndicador->setDado("abreviatura", $this->getAbreviatura());
        $this->obTMONIndicador->setDado("precisao", $this->getPrecisao());
        $obErro = $this->obTMONIndicador->alteracao( $boTransacao );

        //Comentado Regra de Conversão, pois a mesma não vai mais existir na ação, conforme ticket #22008
        /*
        if ( !$obErro->ocorreu() ) {
        $this->obTMONFormulaIndicador->setDado("cod_indicador", $this->getCodIndicador());
        $this->obTMONFormulaIndicador->setDado("cod_modulo", $this->getCodModulo());
        $this->obTMONFormulaIndicador->setDado("cod_biblioteca",$this->getCodBiblioteca());
        $this->obTMONFormulaIndicador->setDado("cod_funcao", $this->getCodFuncao());
        $this->obTMONFormulaIndicador->setDado("inicio_vigencia",$this->getDtVigenciaAntes());
        $obErro = $this->obTMONFormulaIndicador->alteracao( $boTransacao );
        }
        */
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTMONIndicador );

    return $obErro;

}

/**
    * Altera os dados referentes ao INDICADOR
    * @access Public
    * @param  Object $obTransacao Parametro Transacao
    * @return Object Objeto Erro
*/
function AlterarFormulaIndicador($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = new Erro;

    if ( $this->VerificaVigencia () ) {

        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTMONFormulaIndicador->setDado("cod_indicador", $this->getCodIndicador());
            $this->obTMONFormulaIndicador->setDado("cod_modulo", $this->getCodModulo());
            $this->obTMONFormulaIndicador->setDado("cod_biblioteca",$this->getCodBiblioteca());
            $this->obTMONFormulaIndicador->setDado("cod_funcao", $this->getCodFuncao());
            $this->obTMONFormulaIndicador->setDado("inicio_vigencia",$this->getDtVigencia());
            $obErro = $this->obTMONFormulaIndicador->inclusao( $boTransacao );
        //   $this->obTMONFormulaIndicador->debug(); //die();
        }
    } else {
        $obErro->setDescricao("Indicador com data igual ou anterior ao registro atual ". $this->getDescricao());
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTMONFormulaIndicador );

  return $obErro;

}

/**
    * Altera os dados referentes ao INDICADOR
    * @access Public
    * @param  Object $obTransacao Parametro Transacao
    * @return Object Objeto Erro
*/
function AlterarValorIndicador($boTransacao = "")
{
 $obErro= new Erro;
 $boFlagTransacao = false;

 $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {

        $this->obTMONValorIndicador->setDado("cod_indicador", $this->getCodIndicador());
        $this->obTMONValorIndicador->setDado("inicio_vigencia",$this->getDtVigenciaAntes());
        $this->obTMONValorIndicador->setDado("valor",$this->getValor());
        $obErro = $this->obTMONValorIndicador->alteracao( $boTransacao );
        //$this->obTMONValorIndicador->debug();

 }
 $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTMONValorIndicador );

 return $obErro;
}

/**
    * Exclui os dados referentes ao INDICADOR
    * @access Public
    * @param  Object $obTransacao Parametro Transacao
    * @return Object Objeto Erro
*/
function ExcluirIndicador($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    $cdg = $this->getCodIndicador();

    if ( !$obErro->ocorreu() ) {
        $this->obTMONFormulaIndicador->setDado( "cod_indicador" , $cdg );
        $this->obTMONFormulaIndicador->setDado( "cod_funcao",$this->getCodFuncao());
        $this->obTMONFormulaIndicador->setDado( "cod_modulo",$this->getCodModulo());
        $this->obTMONFormulaIndicador->setDado( "cod_biblioteca", $this->getCodBiblioteca());
        $obErro = $this->obTMONFormulaIndicador->exclusao( $boTransacao );
        //     $this->obTMONFormulaIndicador->debug();

        if ( !$obErro->ocorreu() ) {
            $this->obTMONValorIndicador->setDado( "cod_indicador" , $cdg );
            $this->obTMONValorIndicador->setDado( "valor", $this->getValor());
            $obErro = $this->obTMONValorIndicador->exclusao( $boTransacao );
            //   $this->obTMONValorIndicador->debug();

            if ( !$obErro->ocorreu() ) {
                $this->obTMONIndicador->setDado( "cod_indicador" , $cdg );
                $this->obTMONIndicador->setDado( "descricao",$this->getDescricao());
                $this->obTMONIndicador->setDado( "abreviatura", $this->getAbreviatura());
                $this->obTMONIndicador->setDado( "precisao", $this->getPrecisao());
                $obErro = $this->obTMONIndicador->exclusao( $boTransacao );
                //      $this->obTMONIndicador->debug();
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTMONIndicador );

    return $obErro;
}

/**
    * Exclui os dados referentes ao INDICADOR
    * @access Public
    * @param  Object $obTransacao Parametro Transacao
    * @return Object Objeto Erro
*/
function ExcluirValor($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $this->obTMONValorIndicador->setDado( "cod_indicador",$this->getCodIndicador());
        $this->obTMONValorIndicador->setDado( "inicio_vigencia", $this->getDtVigencia());
        $this->obTMONValorIndicador->setDado( "valor", $this->getValor());
        $obErro = $this->obTMONValorIndicador->exclusao( $boTransacao );
        //$this->obTMONValorIndicador->debug();
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTMONIndicador );

    return $obErro;

}

//--------------------------------------------------

/**
* Lista os INDICADORES
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parametro Transacao
* @return Object Objeto Erro
*/
function ListarIndicadores(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";

    if ( $this->getCodIndicador() ) {
        $stFiltro .= " ie.cod_indicador = '".$this->getCodIndicador()."' AND ";
    }
    if ( $this->getDescricao() ) {
        $stFiltro .= " ie.descricao like '%".$this->getDescricao()."%' AND ";
    }
    if ( $this->getAbreviatura () ) {
        $stFiltro .= " ie.abreviatura like '%". $this->getAbreviatura()."%' AND ";
    }
    if ( $this->getPrecisao () ) {
        $stFiltro .= " ie.precisao = ". $this->getPrecisao()." AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrder = '';
    $stOrder.= " ORDER BY ie.cod_indicador";

    $obErro = $this->obTMONIndicador->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    //$this->obTMONIndicador->debug();
    return $obErro;
}

/**
* Lista os VALORES PARA EXCLUSAO (necessita que o valor esteja linkado com um identificador)
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parametro Transacao
* @return Object Objeto Erro
*/
function ListarValoresExclusao(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    $x = explode ('/', $this->getDtVigencia());
    $dia = $x[2].'-'.$x[1].'-'.$x[0];

    if ( $this->getCodIndicador() ) {
        $stFiltro .= " vi.cod_indicador = '".$this->getCodIndicador()."' AND ";
    }
    if ( $this->getDescricao() ) {
        $stFiltro .= " upper(ie.descricao) like '%". strtoupper ( $this->getDescricao())."%' AND ";
    }
    if ( $this->getDtVigencia () ) {
        $stFiltro .= " inicio_vigencia = '". $dia ."' AND ";
    }

    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrder = '';
    $stOrder.= " ORDER BY vi.cod_indicador";

    $obErro = $this->obTMONValorIndicador->recuperaRelacionamentoExclusao( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    //    $this->obTMONValorIndicador->debug();
    return $obErro;
}

/**
* Lista os VALORES PARA ALTERACAO ( lista os indicadores que TEM pelo menos 1 registro de valor
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parametro Transacao
* @return Object Objeto Erro
*/
function ListarValoresAlteracao(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";

    $x = explode ('/', $this->getDtVigencia());
    $dia = $x[2].'-'.$x[1].'-'.$x[0];

    if ( $this->getCodIndicador() ) {
        $stFiltro .= " ie.cod_indicador = '".$this->getCodIndicador()."' AND ";
    }
    if ( $this->getDescricao() ) {
        $stFiltro .= " upper(ie.descricao) like '%". strtoupper ( $this->getDescricao())."%' AND ";
    }
    if ( $this->getDtVigencia () ) {
        $stFiltro .= " inicio_vigencia = '". $dia ."' AND ";
    }

    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }

    $stOrder = ' ORDER BY ie.cod_indicador, inicio_vigencia DESC';

    $obErro = $this->obTMONValorIndicador->recuperaRelacionamentoAlteracao( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    //$this->obTMONValorIndicador->debug();
    return $obErro;
}

/**
* Lista os VALORES PARA INSERCAO (lista todos os indicadores SEM valor registrado
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parametro Transacao
* @return Object Objeto Erro
*/
function ListarValoresInclusao(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";

    if ( $this->getCodIndicador() ) {
        $stFiltro .= " ie.cod_indicador = '".$this->getCodIndicador()."' AND ";
    }
    if ( $this->getDescricao() ) {
        $stFiltro .= " ie.descricao like '%".$this->getDescricao()."%' AND ";
    }
    if ( $this->getAbreviatura () ) {
        $stFiltro .= " ie.abreviatura like '%". $this->getAbreviatura()."%' AND ";
    }

    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }

    $obErro = $this->obTMONValorIndicador->recuperaRelacionamentoInclusao( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    //   $this->obTMONValorIndicador->debug();
    return $obErro;
}

/**
    * Recupera do banco de dados os dados do INDICADOR SELECIONADO
    * @access Public
    * @param  Object $obTransacao Parametro Transacao
    * @return Object Objeto Erro
*/
function ConsultarIndicador($boTransacao = "")
{
    if ( $this->getCodIndicador() ) {
        $stFiltro = "\r\n\t WHERE cod_indicador = ".$this->getCodIndicador();
        $obErro = $this->obTMONIndicador->recuperaTodos( $rsRecordSet, $stFiltro, $boTransacao );
        $this->obTMONIndicador->debug();
        if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
            $this->inCodIndicador = $rsRecordSet->getCampo("cod_indicador");
            $this->stDescricao    = $rsRecordSet->getCampo("descricao");

        }
    }

    return $obErro;
}

/**
    * Verifica se o INDICADOR a ser incluido já existe
    * @access Public
    * @param  Object $rsBanco Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parametro Transacao
    * @return Object Objeto Erro
*/
function VerificaIndicador($boTransacao = "")
{
    $obErro = $this->obTMONIndicador->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    $cont =0;
    $achou = false;
    $valores = Array ();
    while ($cont < $rsRecordSet->getNumLinhas()) {

        $valores[$cont] = strtoupper ($rsRecordSet->getCampo("descricao"));

        if ( $valores[$cont] == strtoupper ($this->getDescricao()) ) {
            $achou = true; break;
        }
        $cont++;
        $rsRecordSet->proximo();
    }

    if ( $rsRecordSet->getNumLinhas() > 0 && $achou ) {
        $obErro->setDescricao("Indicador já cadastrado no Sistema! ". $this->getDescricao());
    }

    return $obErro;
}

/**
    * Busca os valores dos códigos da formula do INDICADOR ECONOMICO
    * @access Public
    * @param  Object $obTransacao Parametro Transacao, codigo do indicador
    * @return Object Objeto Erro
*/
function DevolveFormula(&$rsRecordSet , $inCodIndicador, $boTransacao='')
{
    $obErro = $this->obTMONFormulaIndicador->RecuperaRelacionamentoDadosDaFormula( $rsRecordSet, $stFiltro, $stOrder, $boTransacao, $inCodIndicador );
    if ( !$obErro->ocorreu () ) {

        $this->setCodIndicador    ($rsRecordSet->getCampo("cod_acrescimo"));
        $this->setCodModulo       ($rsRecordSet->getCampo("cod_modulo"));
        $this->setCodBiblioteca   ($rsRecordSet->getCampo("cod_biblioteca"));
        $this->setCodFuncao       ($rsRecordSet->getCampo("cod_funcao"));
        $this->setDtVigencia      ($rsRecordSet->getCampo("inicio_vigencia"));

        $this->DevolveDescFormula ( $rsRecordSet , $boTransacao='', $rsRecordSet->getCampo("cod_modulo"), $rsRecordSet->getCampo("cod_biblioteca"), $rsRecordSet->getCampo("cod_funcao") );

    }

   return $obErro;
}

/**
    * Busca a descricao da formula. chamada na funcao DevolveFormula
    * @access Public
    * @param  Object $obTransacao Parametro Transacao
    * @return Object Objeto Erro
*/
function DevolveDescFormula(&$rsRecordSet, $boTransacao='')
{
    $codMod  = $rsRecordSet->getCampo("cod_modulo");
    $codBib  = $rsRecordSet->getCampo("cod_biblioteca");
    $codFunc = $rsRecordSet->getCampo("cod_funcao");

    $obErro = $this->obTMONFormulaIndicador->RecuperaRelacionamentoDescricaoDaFormula( $rsRecordSet, $stFiltro, $stOrder, $boTransacao, $codMod, $codBib, $codFunc );
    if ( !$obErro->ocorreu () ) {
        $this->StrFormula = $rsRecordSet->getCampo("nom_funcao");
    }

   return $obErro;
}

/**
    * Busca o valor do Indicador Economico
    * @access Public
    * @param  Object $obTransacao Parametro Transacao
    * @return Object Objeto Erro
*/
function DevolveValor(&$rsRecordSet , $codigo , $boTransacao='')
{
    $obErro = $this->obTMONValorIndicador->RecuperaRelacionamentoUltimaVigencia( $rsRecordSet, $stFiltro, $stOrder, $boTransacao, $codigo );
    if ( !$obErro->ocorreu () ) {
        $this->inValor    = $rsRecordSet->getCampo("valor");
        $this->dtVigencia = $rsRecordSet->getCampo("inicio_vigencia");
    }
}

/**
    * Verifica as datas de vigencia do Indicador Economico na FORMULA_INDICADOR
    * @access Public
    * @param  Object $obTransacao Parametro Transacao
    * @return Object Objeto Erro
*/
function VerificaVigencia()
{
    $x  = explode ('/', $this->getDtVigenciaAntes());
    $diaAntes = $x[2].$x[1].$x[0];

    $x = explode ('/', $this->getDtVigencia());
    $diaAgora = $x[2].$x[1].$x[0];

    if ($diaAgora <= $diaAntes) {
        $ok = false;
    } else {
        $ok = true;
    }

    return $ok;
}

/**
    * Verifica as datas de vigencia do Indicador Economico no VALOR
    * @access Public
    * @param  Object $obTransacao Parametro Transacao
    * @return Object Objeto Erro
*/
function VerificaVigenciaValor()
{
    $this->obErro = new Erro;

    $x = explode ('/', $this->getDtVigenciaAntes());
    $diaAntes = $x[2].$x[1].$x[0];
    $x = explode ('/', $this->getDtVigencia());
    $diaAgora = $x[2].$x[1].$x[0];

    if ($diaAgora < $diaAntes) { //se a data de viagencia atual for anterior à + recente
         $this->obErro->setDescricao ('inserir valor com data vigente igual ou anterior à atual [ '.  $this->getDtVigenciaAntes ().' ]');

         return $this->obErro;
    } elseif ($diaAgora == $diaAntes) { //se a data for a mesma, está apenas atualizando o valor
        $this->inAcaoAlteraValor = 1;
    } else {
        $this->inAcaoAlteraValor = 2;
    }

return $this->obErro;
}
}
