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

    * Classe de Regra de Plano Conta
    * Data de Criação   : 03/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Author: cako $
    $Date: 2007-12-04 12:08:26 -0200 (Ter, 04 Dez 2007) $

    * Casos de uso: uc-02.02.02, uc-02.04.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"                     );
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadeSistemaContabil.class.php"         );
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadeClassificacaoContabil.class.php"   );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"   );
include_once (CAM_GRH_ENT_MAPEAMENTO."TEntidade.class.php");

/**
    * Classe de Regra de Plano Conta
    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo
*/
class RContabilidadePlanoConta
{
/**
    * @access Private
    * @var Object
*/
var $obRContabilidadeSistemaContabil;
/**
    * @access Private
    * @var Object
*/
var $obRContabilidadeClassificacaoContabil;
/**
    * @access Private
    * @var Object
*/
var $obROrcamentoEntidade;
/**
    * @access Private
    * @var Object
*/
var $obTransacao;
/**
    * @access Private
    * @var Integer
*/
var $inCodConta;
/**
    * @access Private
    * @var String
*/
var $stNomConta;
/**
    * @access Private
    * @var String
*/
var $stExercicio;
/**
    * @access Private
    * @var String
*/
var $stCodEstrutural;
/**
    * @access Private
    * @var Boolean
*/
var $boContaAnalitica;
/**
    *@access Private
    *@var Array
*/
var $arGrupos;

//parametros usados somente na base do tce do mato grosso do sul
/**
    *@access Private
    *@var String
*/
var $stEscrituracao;
/**
    *@access Private
    *@var String
*/
var $stNaturezaSaldo;
/**
    *@access Private
    *@var String
*/
var $stIndicadorSuperavit;
/**
    *@access Private
    *@var String
*/
var $stFuncao;
/**
    *@access Private
    *@var String
*/
var $inTipoContaCorrenteTCEPE;
/**
    *@access Private
    *@var Integer
*/
var $inTipoContaCorrenteTCEMG;

/**
    * @access Public
    * @param Object $Valor
*/
function setRContabilidadeSistemaContabil($valor) { $this->obRContabilidadeSistemaContabil = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setRContabilidadeClassificacaoContabil($valor) { $this->obRContabilidadeClassificacaoContabil = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setROrcamentoEntidade($valor) { $this->obROrcamentoEntidade = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTransacao($valor) { $this->obTransacao = $valor; }

/**
    * @access Public
    * @param Integer $Valor
*/
function setCodConta($valor) { $this->inCodConta  = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setNomConta($valor) { $this->stNomConta  = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setExercicio($valor) { $this->stExercicio = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setCodEstrutural($valor) { $this->stCodEstrutural = $valor; }
/**
    * @access Public
    * @param Boolean $Valor
*/
function setContaAnalitica($valor) { $this->boContaAnalitica = $valor; }
/**
    * @access public
    * @param object $valor
*/
function setGrupos($valor) { $this->arGrupos      = $valor;        }

//parametros usados somente na base do tce do mato grosso do sul
/**
    * @access public
    * @param String $valor
*/
function setEscrituracao($valor) { $this->stEscrituracao      = $valor;        }
/**
    * @access public
    * @param String $valor
*/
function setNaturezaSaldo($valor) { $this->stNaturezaSaldo      = $valor;        }
/**
    * @access public
    * @param String $valor
*/
function setIndicadorSuperavit($valor) { $this->stIndicadorSuperavit      = $valor;        }
/**
    * @access public
    * @param String $valor
*/
function setFuncao($valor) { $this->stFuncao      = $valor;        }
/**
    * @access Public
    * @param Integer $Valor
*/
function setTipoContaCorrenteTCEPE($valor) { $this->inTipoContaCorrenteTCEPE  = $valor; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setTipoContaCorrenteTCEMG($valor) { $this->inTipoContaCorrenteTCEMG  = $valor; }

/**
    * @access Public
    * @return Object
*/
function getRContabilidadeSistemaContabil() { return $this->obRContabilidadeSistemaContabil; }
/**
    * @access Public
    * @return Object
*/
function getRContabilidadeClassificacaoContabil() { return $this->obRContabilidadeClassificacaoContabil; }
/**
    * @access Public
    * @return Object
*/
function getROrcamentoEntidade() { return $this->obROrcamentoEntidade; }
/**
    * @access Public
    * @return Object
*/
function getTransacao() { return $this->obTransacao; }
/**
    * @access Public
    * @return Integer
*/
function getCodConta() { return $this->inCodConta;  }
/**
    * @access Public
    * @return String
*/
function getNomConta() { return $this->stNomConta;  }
/**
    * @access Public
    * @return String
*/
function getExercicio() { return $this->stExercicio; }
/**
    * @access Public
    * @return String
*/
function getCodEstrutural() { return $this->stCodEstrutural; }
/**
    * @access Public
    * @return Boolean
*/
function getContaAnalitica() { return $this->boContaAnalitica; }
/**
    * @access Public
    * @return Object
*/
function getGrupos() { return $this->arGrupos;      }

//parametros usados somente na base do tce do mato grosso do sul
/**
    * @access public
    * @return String
*/
function getEscrituracao() { return $this->stEscrituracao; }
/**
    * @access public
    * @return String
*/
function getNaturezaSaldo() { return $this->stNaturezaSaldo; }
/**
    * @access public
    * @return String
*/
function getIndicadorSuperavit() { return $this->stIndicadorSuperavit; }
/**
    * @access public
    * @return String
*/
function getFuncao() { return $this->stFuncao; }
/**
    * @access Public
    * @return Integer
*/
function getTipoContaCorrenteTCEPE() { return $this->inTipoContaCorrenteTCEPE; }
/**
    * @access Public
    * @return Integer
*/
function getTipoContaCorrenteTCEMG() { return $this->inTipoContaCorrenteTCEMG; }
/**
     * Método construtor
     * @access Public
*/
function RContabilidadePlanoConta()
{
    $this->obRContabilidadeClassificacaoContabil = new RContabilidadeClassificacaoContabil;
    $this->obRContabilidadeSistemaContabil       = new RContabilidadeSistemaContabil;
    $this->obROrcamentoEntidade                  = new ROrcamentoEntidade;
    $this->obTransacao                           = new Transacao;
}

/**
    * Executa um pegaConfiguracao na classe TConfiguracao
    * @access Public
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function recuperaMascaraConta(&$stValor, $boTransacao = "")
{
    include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );
    $obTAdministracaoConfiguracao          = new TAdministracaoConfiguracao;

    $obTAdministracaoConfiguracao->setDado( "cod_modulo", 9);
    $obTAdministracaoConfiguracao->setDado( "exercicio", $this->stExercicio );
    $obTAdministracaoConfiguracao->setDado( "parametro", "masc_plano_contas" );
    $obErro = $obTAdministracaoConfiguracao->pegaConfiguracao( $stValor, '', $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaPorChave na classe Persistente
    * @access Public
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultar($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoConta.class.php"         );
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoContaTCEMS.class.php"         );
    if ( Sessao::getExercicio() > '2012' ) {
        $obTContabilidadePlanoConta = new TContabilidadePlanoContaTCEMS;
    } else {
        $obTContabilidadePlanoConta = new TContabilidadePlanoConta;
    }

    if ($this->inCodConta) {
        $obTContabilidadePlanoConta->setDado( "cod_conta", $this->inCodConta  );
        $obTContabilidadePlanoConta->setDado( "exercicio", $this->stExercicio );
        $obErro = $obTContabilidadePlanoConta->recuperaPorChave( $rsRecordSet, $boTransacao );
    } else {
        $stFiltro = " WHERE cod_estrutural = '".$this->stCodEstrutural."' AND exercicio = '".$this->stExercicio."' ";
        $obErro = $obTContabilidadePlanoConta->recuperaTodos( $rsEstrutural, $stFiltro,'', $boTransacao );
        if ( !$obErro->ocorreu() ) {
            if ( $rsEstrutural->getCampo('cod_conta') ) {
                $this->inCodConta = $rsEstrutural->getCampo('cod_conta');
                $obErro = $this->consultar( $boTransacao );
            }

            return $obErro;
        }
    }
    if ( !$obErro->ocorreu() ) {
        $this->stNomConta = $rsRecordSet->getCampo( "nom_conta" );
        $this->stCodEstrutural = $rsRecordSet->getCampo( "cod_estrutural" );
        if ( Sessao::getExercicio() > '2012' ) {
            $this->stIndicadorSuperavit = trim($rsRecordSet->getCampo( "indicador_superavit" ));
            $this->stEscrituracao = trim($rsRecordSet->getCampo( "escrituracao" ));
            $this->stFuncao = trim($rsRecordSet->getCampo( "funcao" ));
            $this->stNaturezaSaldo = trim($rsRecordSet->getCampo( "natureza_saldo" ));
            $this->inTipoContaCorrenteTCEPE = $rsRecordSet->getCampo( "atributo_tcepe" );
            $this->inTipoContaCorrenteTCEMG = $rsRecordSet->getCampo( "atributo_tcemg" );
        }       
        $this->obRContabilidadeClassificacaoContabil->setCodClassificacao( $rsRecordSet->getCampo( "cod_classificacao" ) );
        $this->obRContabilidadeSistemaContabil->setCodSistema( $rsRecordSet->getCampo( "cod_sistema" ) );
        $this->obRContabilidadeSistemaContabil->setExercicio ( $this->stExercicio );
        $obErro = $this->obRContabilidadeSistemaContabil->consultar( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obRContabilidadeClassificacaoContabil->setExercicio       ( $this->stExercicio );
            $obErro = $this->obRContabilidadeClassificacaoContabil->consultar( $boTransacao );
        }
    }

    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listar(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoConta.class.php"         );
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoContaTCEMS.class.php"         );
    if ( Sessao::getExercicio() > '2012' ) {
        $obTContabilidadePlanoConta = new TContabilidadePlanoContaTCEMS;
    } else {
        $obTContabilidadePlanoConta = new TContabilidadePlanoConta;
    }

    if($this->inCodConta)
        $stFiltro  = " cod_conta = " . $this->inCodConta . "  AND ";
    if($this->stExercicio)
        $stFiltro .= " exercicio = '" . $this->stExercicio . "' AND ";
    if($this->stNomDescricao)
        $stFiltro .= " nom_conta like '" . $this->stNomConta . "%' AND ";
    if($this->stCodEstrutural)
        $stFiltro .= " publico.fn_mascarareduzida(cod_estrutural) like (publico.fn_mascarareduzida('".$this->stCodEstrutural."')||'%') AND ";
    if($this->inCodSistema)
        $stFiltro .= " cod_sistema = " . $this->inCodSistema . " AND ";
    if ( Sessao::getExercicio() > '2012' ) {
        if($this->stEscrituracao)
            $stFiltro .= " escrituracao = '" . $this->stEscrituracao . "' AND ";
        if($this->stNaturezaSaldo)
            $stFiltro .= " natureza_saldo = '" . $this->stNaturezaSaldo . "' AND ";
        if($this->stIndicadorSuperavit)
            $stFiltro .= " indicador_superavit = '" . $this->stIndicadorSuperavit . "' AND ";
        if($this->stFuncao)
            $stFiltro .= " funcao = '" . $this->stFuncao . "' AND ";
    }
    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $obErro = $obTContabilidadePlanoConta->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaGrupos na Tabela ContabilidadeAnalitica
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarGrupos(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoConta.class.php" );
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoContaTCEMS.class.php" );
    if ( Sessao::getExercicio() > '2012' ) {
        $obTContabilidadePlanoConta = new TContabilidadePlanoContaTCEMS;
    } else {
        $obTContabilidadePlanoConta = new TContabilidadePlanoConta;
    }

    if($this->inCodConta)
        $stFiltro  = " cod_conta = " . $this->inCodConta . "  AND ";
    if($this->stExercicio)
        $stFiltro .= " exercicio = '" . $this->stExercicio . "' AND ";
    if($this->stNomDescricao)
        $stFiltro .= " nom_conta like '" . $this->stNomConta . "%' AND ";
    if($this->stCodEstrutural)
        $stFiltro .= " publico.fn_mascarareduzida(cod_estrutural) like (publico.fn_mascarareduzida('".$this->stCodEstrutural."')||'%') AND ";
    if($this->inCodSistema)
        $stFiltro .= " cod_sistema = " . $this->inCodSistema . " AND ";
    if ( Sessao::getExercicio() > '2012' ) {
        if($this->stEscrituracao)
            $stFiltro .= " escrituracao = '" . $this->stEscrituracao . "' AND ";
        if($this->stNaturezaSaldo)
            $stFiltro .= " natureza_saldo = '" . $this->stNaturezaSaldo . "' AND ";
        if($this->stIndicadorSuperavit)
            $stFiltro .= " indicador_superavit = '" . $this->stIndicadorSuperavit . "' AND ";
        if($this->stFuncao)
            $stFiltro .= " funcao = '" . $this->stFuncao . "' AND ";
    }
//    if ( sizeof($this->arGrupos) ) {
//        $stFiltro .= " substr( cod_estrutural,1,1 ) IN ( "
//        foreach ($this->arGrupos as $value) {
//            $stSubFiltro .= $value . ' , ';
//        }
//        $stFiltro .= substr($stSubFiltro,0,strlen($stSubFiltro)-2);
//        $stFiltro .= " ) AND ";
//    }
    $stFiltro = ($stFiltro) ? " AND " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $obErro = $obTContabilidadePlanoConta->recuperaGrupos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @return String String do Código de Classificacao Pai
*/

function gerarCodigoEstruturalPai()
{
    $arCodEstrutural = explode( '.', $this->stCodEstrutural );
    $inCount = count( $arCodEstrutural );

    for ( $inPosicao = ($inCount-1); $inPosicao >= 0; $inPosicao-- ) {
        if ($arCodEstrutural[$inPosicao] != 0) {
            if ($inPosicao != 0) {
                $arCodEstruturalPai = $arCodEstrutural;
                $inTamPos = strlen( $arCodEstrutural[$inPosicao]);
                $arCodEstruturalPai[$inPosicao] = str_pad('',$inTamPos,'0');
                break;
            }
        }
    }
    $stCodEstruturalPai = @implode( '.',$arCodEstruturalPai );

    return $stCodEstruturalPai;
}

/**
    * Valida se Codigo Estrutural jah existe
    * @access Public
    * @param Boolean $boTransacao
    * @return Object Objeto Erro
*/
function validarCodigoEstrutural($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoConta.class.php" );
    $obTContabilidadePlanoConta = new TContabilidadePlanoConta;
    $stFiltro = " WHERE cod_estrutural = '".$this->stCodEstrutural."' ";

    $stFiltro.= " AND exercicio = '".$this->stExercicio."' ";

    $obErro = $obTContabilidadePlanoConta->recuperaTodos( $rsRecordSet, $stFiltro, '', $boTransacao );
    if ( !$rsRecordSet->eof()  and !$obErro->ocorreu() ) {
        $obErro->setDescricao( "Código de classificação já existe." );
    }

    return $obErro;

}

/**
    * Valida se Codigo Estrutural tem pai
    * @access Public
    * @param Boolean $boTransacao
    * @return Object Objeto Erro
*/
function validarCodigoEstruturalPai($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoConta.class.php" );
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoContaTCEMS.class.php" );
    if ( Sessao::getExercicio() > '2012' ) {
        $obTContabilidadePlanoConta = new TContabilidadePlanoContaTCEMS;
    } else {
        $obTContabilidadePlanoConta = new TContabilidadePlanoConta;
    }

    $obErro = new Erro;
    $stCodEstruturalPai = $this->gerarCodigoEstruturalPai();
    if ($stCodEstruturalPai) {
        if ( Sessao::getExercicio() > '2012' ) {
            $stCodEstruturalPaiTCEMS = substr($stCodEstruturalPai, 0, 12);
            $stFiltro = ' WHERE cod_estrutural like \''.$stCodEstruturalPaiTCEMS.'%\' ';
        } else {
            $stFiltro = " WHERE cod_estrutural = '$stCodEstruturalPai' ";
        }
        $obErro = $obTContabilidadePlanoConta->recuperaTodos( $rsRecordSet, $stFiltro, '', $boTransacao );
        if ( $rsRecordSet->eof()  and !$obErro->ocorreu() ) {
            $obErro->setDescricao( "Não existe classificação mãe para esta conta" );
        }
    }

    return $obErro;
}

/**
    * Valida se Codigo Estrutural tem filhos
    * @access Public
    * @param Boolean $boTransacao
    * @return Object Objeto Erro
*/
function validarCodigoEstruturalFilho($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoConta.class.php" );

    $obTContabilidadePlanoConta = new TContabilidadePlanoConta;

    $stFiltro  = " WHERE publico.fn_mascarareduzida(cod_estrutural) like (publico.fn_mascarareduzida('".$this->stCodEstrutural."')||'%') AND ";
    if ( Sessao::getExercicio() > '2012' ) {
        $stFiltro .= " publico.fn_mascara_completa((select valor from administracao.configuracao where cod_modulo = 9 and
            exercicio = '".$this->stExercicio."' and parametro = 'masc_plano_contas'), cod_estrutural) != '".$this->stCodEstrutural."' ";
    } else {
        $stFiltro .= " cod_estrutural != '".$this->stCodEstrutural."' ";
    }
    if($this->stExercicio)
        $stFiltro .= " AND exercicio = '".$this->stExercicio."' ";
    $obErro = $obTContabilidadePlanoConta->recuperaTodos( $rsRecordSet, $stFiltro, '', $boTransacao );

    if ( !$rsRecordSet->eof()  and !$obErro->ocorreu() ) {
        $obErro->setDescricao( "Conta não pode ser excluída porque possui lançamentos." );
    }

    return $obErro;
}

function validarNivelConta($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoConta.class.php" );
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoContaTCEMS.class.php" );
    if ( Sessao::getExercicio() > '2012' ) {
        $obTContabilidadePlanoConta = new TContabilidadePlanoContaTCEMS;
    } else {
        $obTContabilidadePlanoConta = new TContabilidadePlanoConta;
    }

    $obTContabilidadePlanoConta->setDado( "exercicio"         , $this->stExercicio        );

    if ( Sessao::getExercicio() > '2012' ) {
        $obErro = $this->recuperaMascaraConta( $stMascara, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obTContabilidadePlanoConta->setDado( "cod_estrutural"    , SistemaLegado::doMask( $this->stCodEstrutural, $stMascara ) );
        }
    } else {
        if (strlen($this->stCodEstrutural) == 24) {
            $obTContabilidadePlanoConta->setDado( "cod_estrutural"    , $this->stCodEstrutural    );
        }
    }

    $obErro = $obTContabilidadePlanoConta->recuperaNivelConta( $rsRecordSet, $stFiltro, '', $boTransacao );

    if ( !Sessao::getExercicio() > '2012' ) {
        if (strlen($this->stCodEstrutural) != 24) {
            $obErro->setDescricao('Código de classificação inválido!');
        }
    }

    if (!$this->getContaAnalitica()) {
        if ( !$rsRecordSet->eof()  and !$obErro->ocorreu() ) {
            if ($rsRecordSet->getCampo("nivel_conta")==$rsRecordSet->getCampo("nivel_maximo")) {
                $obErro->setDescricao( "Esta conta já está desdobrada no último nível. Cadastrar como Analítica." );
            }
        }
    }

    return $obErro;
}

/**
    * Verifica o tipo de Conta da classe pai
    * @access Public
    * @param Boolean $boTransacao
    * @return Object Objeto Erro
*/
function checarContaPai($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoConta.class.php" );
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoContaTCEMS.class.php" );
    if ( Sessao::getExercicio() > '2012' ) {
        $obTContabilidadePlanoConta = new TContabilidadePlanoContaTCEMS;
    } else {
        $obTContabilidadePlanoConta = new TContabilidadePlanoConta;
    }

    $stCodEstruturalPai = $this->gerarCodigoEstruturalPai();
    $stFiltro = " WHERE cod_estrutural = '".$stCodEstruturalPai."' ";
    $stFiltro.= "   AND exercicio = '".$this->stExercicio."'";
    $obErro = $obTContabilidadePlanoConta->recuperaTodos( $rsRecordSet, $stFiltro, '', $boTransacao );
    if ( !$rsRecordSet->eof()  and !$obErro->ocorreu() ) {
        $stFiltro = " AND pa.exercicio = '".$this->stExercicio."' AND pa.cod_conta = ".trim($rsRecordSet->getCampo( "cod_conta" ));
        $obErro = $obTContabilidadePlanoConta->recuperaContaAnalitica( $rsRecordSet, $stFiltro, '', $boTransacao );
        if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
            $obErro->setDescricao( "Conta mãe é analítica" );
        }
    }

    return $obErro;
}

/**
    * Salva dados do Plano de Conta no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function salvar($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoConta.class.php"         );
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoContaTCEMS.class.php" );
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeClassificacaoPlano.class.php" );
    $obTContabilidadeClassificacaoPlano    = new TContabilidadeClassificacaoPlano;
    if ( Sessao::getExercicio() > '2012' ) {
        $obTContabilidadePlanoConta = new TContabilidadePlanoContaTCEMS;
    } else {
        $obTContabilidadePlanoConta = new TContabilidadePlanoConta;
    }

    $boFlagTransacao = false;
    $boFlagNovaClassificacao = true;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao);
    
    if ( !$obErro->ocorreu() ) {
        //valida o estrutural caso seja passa com mascara diferente
        $stCodEstruturalComMascara = SistemaLegado::doMask($this->stCodEstrutural, '', '0', $boTransacao);
        
        if ($this->stCodEstrutural == $stCodEstruturalComMascara) {
            $this->stCodEstrutural = $stCodEstruturalComMascara;
        }

        $obTContabilidadePlanoConta->setDado( "exercicio"         , $this->stExercicio        );
        $obTContabilidadePlanoConta->setDado( "nom_conta"         , $this->stNomConta         );
        $obTContabilidadePlanoConta->setDado( "cod_estrutural"    , $this->stCodEstrutural    );
        $obTContabilidadePlanoConta->setDado( "cod_classificacao" , $this->obRContabilidadeClassificacaoContabil->getCodClassificacao() );
        $obTContabilidadePlanoConta->setDado( "cod_sistema"       , $this->obRContabilidadeSistemaContabil->getCodSistema() );
        $obTContabilidadePlanoConta->setDado( "cod_estrutural"    , $this->stCodEstrutural );
        if ( Sessao::getExercicio() > '2012' ) {
            $obTContabilidadePlanoConta->setDado( "escrituracao"    , $this->stEscrituracao );
            $obTContabilidadePlanoConta->setDado( "natureza_saldo"    , $this->stNaturezaSaldo );
            $obTContabilidadePlanoConta->setDado( "indicador_superavit"    , $this->stIndicadorSuperavit );
            $obTContabilidadePlanoConta->setDado( "funcao"    , $this->stFuncao );
        }
 
        if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao) == 16) {
            $obTContabilidadePlanoConta->setDado( "atributo_tcepe" , $this->inTipoContaCorrenteTCEPE );
        }
        if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao) == 11) {
            $obTContabilidadePlanoConta->setDado( "atributo_tcemg" , $this->inTipoContaCorrenteTCEMG );
        }
        $obErro = $this->validarCodigoEstruturalPai( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            
            $obErro = $this->checarContaPai( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                
                $obErro = $this->validarNivelConta( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    
                    if ($this->inCodConta) {
                        $boFlagNovaClassificacao = false;
                        $obTContabilidadePlanoConta->setDado( "cod_conta", $this->inCodConta );
                        $obErro = $obTContabilidadePlanoConta->alteracao( $boTransacao );
                    } else {

                        $obErro = $this->validarCodigoEstrutural( $boTransacao );
                        if ( !$obErro->ocorreu() ) {

                            $obTContabilidadePlanoConta->proximoCod( $inCodConta, $boTransacao );
                            $this->setCodConta( $inCodConta );
                            $obTContabilidadePlanoConta->setDado( "cod_conta" , $this->inCodConta );
                            $obErro = $obTContabilidadePlanoConta->inclusao( $boTransacao );
                        }
                    }
                    
                    if ( !$obErro->ocorreu() ) {
                        $obTContabilidadeClassificacaoPlano->setDado( "exercicio", $this->stExercicio );
                        $obTContabilidadeClassificacaoPlano->setDado( "cod_conta", $this->inCodConta  );

                        $arCodEstrutural = explode('.',$this->stCodEstrutural);
                        $inCount = count($arCodEstrutural);
                        
                        for ($inPosicao = 1; $inPosicao <= $inCount; $inPosicao++) {
                            $obTContabilidadeClassificacaoPlano->setDado( "cod_classificacao" , $arCodEstrutural[($inPosicao-1)] );
                            $obTContabilidadeClassificacaoPlano->setDado( "cod_posicao", ( $inPosicao ) );
                            
                            if( $boFlagNovaClassificacao ) {
                                $obErro = $obTContabilidadeClassificacaoPlano->inclusao( $boTransacao );
                            } else {
                                $obErro = $obTContabilidadeClassificacaoPlano->alteracao($boTransacao );
                            }
                        }
                    }
                }
            }
        }
    }
       
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

    return $obErro;
}

/**
    * Salva dados do Plano de Conta no banco de dados sem verificar níveis de conta
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function salvarEscolhaPlanoConta($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoConta.class.php"         );
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoContaTCEMS.class.php" );
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeClassificacaoPlano.class.php" );
    $obTContabilidadeClassificacaoPlano    = new TContabilidadeClassificacaoPlano;
    if ( Sessao::getExercicio() > '2012' ) {
        $obTContabilidadePlanoConta = new TContabilidadePlanoContaTCEMS;
    } else {
        $obTContabilidadePlanoConta = new TContabilidadePlanoConta;
    }

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao);
    if ( !$obErro->ocorreu() ) {
        $obTContabilidadePlanoConta->setDado( "exercicio"         , $this->stExercicio        );
        $obTContabilidadePlanoConta->setDado( "nom_conta"         , $this->stNomConta         );
        $obTContabilidadePlanoConta->setDado( "cod_estrutural"    , $this->stCodEstrutural    );
        $obTContabilidadePlanoConta->setDado( "cod_classificacao" , $this->obRContabilidadeClassificacaoContabil->getCodClassificacao() );
        $obTContabilidadePlanoConta->setDado( "cod_sistema"       , $this->obRContabilidadeSistemaContabil->getCodSistema() );
        if ( Sessao::getExercicio() > '2012' ) {
            $obTContabilidadePlanoConta->setDado( "escrituracao"    , $this->stEscrituracao );
            $obTContabilidadePlanoConta->setDado( "natureza_saldo"    , $this->stNaturezaSaldo );
            $obTContabilidadePlanoConta->setDado( "indicador_superavit"    , $this->stIndicadorSuperavit );
            $obTContabilidadePlanoConta->setDado( "funcao"    , $this->stFuncao );
        }
        if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao) == 11) {
            $obTContabilidadePlanoConta->setDado( "atributo_tcemg" , $this->inTipoContaCorrenteTCEMG );
        }
        if ($this->inCodConta) {
            $obTContabilidadePlanoConta->setDado( "cod_conta", $this->inCodConta );
            $obErro = $obTContabilidadePlanoConta->alteracao( $boTransacao );
        } else {
            $obTContabilidadePlanoConta->proximoCod( $inCodConta, $boTransacao );
            $this->setCodConta( $inCodConta );
            $obTContabilidadePlanoConta->setDado( "cod_conta" , $this->inCodConta );
            $obErro = $obTContabilidadePlanoConta->inclusao( $boTransacao  );
        }
    }
    
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

    return $obErro;
}

/**
    * Exclui dados do Plano de Conta do banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluir($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoConta.class.php"         );
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeClassificacaoPlano.class.php" );
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamento.class.php" 	   );

    $obTContabilidadeClassificacaoPlano    = new TContabilidadeClassificacaoPlano;
    $obTContabilidadeLancamento		   	   = new TContabilidadeLancamento;
    $obTContabilidadePlanoConta = new TContabilidadePlanoConta;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() and $this->inCodConta ) {
        $obErro = $this->validarCodigoEstruturalFilho( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            if ( !$obErro->ocorreu() ) {
                $obTContabilidadeClassificacaoPlano->setDado( "cod_conta", $this->inCodConta );
                $obTContabilidadeClassificacaoPlano->setDado( "exercicio", $this->stExercicio  );
                $obErro = $obTContabilidadeClassificacaoPlano->exclusao( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $obTContabilidadePlanoConta->setDado( "cod_conta", $this->inCodConta );
                    $obTContabilidadePlanoConta->setDado( "exercicio", $this->stExercicio  );
                    $obErro = $obTContabilidadePlanoConta->exclusao( $boTransacao );
                        if ( !$obErro->ocorreu() ) {
                            $obTContabilidadeLancamento->setDado( "cod_plano", $this->inCodPlano );
                            $obTContabilidadeLancamento->setDado( "exercicio", $this->stExercicio );
                            $obErro = $obTContabilidadeLancamento->exclusao( $boTransacao );
                        }
                }
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro , $obTContabilidadePlanoConta );
    }

    return $obErro;
}

/**
    * Exclui dados da escolha do Plano de Conta do banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirEscolhaPlanoConta($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoConta.class.php"         );
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeClassificacaoPlano.class.php" );
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamento.class.php"     );

    $obTContabilidadeClassificacaoPlano    = new TContabilidadeClassificacaoPlano;
    $obTContabilidadeLancamento            = new TContabilidadeLancamento;
    $obTContabilidadePlanoConta = new TContabilidadePlanoConta;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() and $this->inCodConta ) {
        $obTContabilidadeClassificacaoPlano->setDado( "cod_conta", $this->inCodConta );
        $obTContabilidadeClassificacaoPlano->setDado( "exercicio", $this->stExercicio  );
        $obErro = $obTContabilidadeClassificacaoPlano->exclusao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obTContabilidadePlanoConta->setDado( "cod_conta", $this->inCodConta );
            $obTContabilidadePlanoConta->setDado( "exercicio", $this->stExercicio  );
            $obErro = $obTContabilidadePlanoConta->exclusao( $boTransacao );
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro , $obTContabilidadePlanoConta );
    }

    return $obErro;
}

/**
    * Método para verificar qual o cod_plano referente a um cod_estrutural em um exercicio especifico
    * @access Private
    * @param Integer $inCodPlano
    * @param Object $boTransacao
    /* @return Object $obErro
*/
function recuperaCodPlanoPorEstrutural(&$stRetorno, $stExercicio, $stCodEstrutural, $boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php" );
    $obTContabilidadePlanoAnalitica = new TContabilidadePlanoAnalitica;

    $obTContabilidadePlanoAnalitica->setDado("exercicio", $stExercicio );
    $obTContabilidadePlanoAnalitica->setDado("cod_estrutural", $stCodEstrutural );
    $obErro = $obTContabilidadePlanoAnalitica->recuperaCodPlanoPorEstrutural($rsRetorno, '','',$boTransacao);
    if ( !$obErro->ocorreu() ) {
        $stRetorno = $rsRetorno->getCampo('cod_plano');
    }

    return $obErro;
}

}
