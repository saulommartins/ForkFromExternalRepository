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
* Classe de negócio para profissão
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 4866 $
$Name$
$Author: lizandro $
$Date: 2006-01-09 16:52:10 -0200 (Seg, 09 Jan 2006) $

Casos de uso: uc-01.07.86
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_CSE_MAPEAMENTO."TProfissao.class.php"   );
include_once ( CAM_GA_CSE_NEGOCIO."RConselho.class.php"         );

/**
    * Classe de Regra de Negócio Profissao
    * Data de Criação   : 27/04/2004
    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
*/

class RProfissao
{
/**
    * @access Private
    * @var String
*/
var $stNomeProfissao;
/**
    * @access Private
    * @var Integer
*/
var $inCodigoProfissao;
/**
    * @access Private
    * @var integer
*/
var $inCodigoConselho;
/**
    * @access Private
    * @var Object
*/
var $obRConselho;
/**
    * @access Private
    * @var Object
*/
var $obTProfissao;

/**
    * @access Public
    * @param String $valor
*/
function setNomeProfissao($valor) { $this->stNomeProfissao   = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoProfissao($valor) { $this->inCodigoProfissao = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoConselho($valor) { $this->inCodigoConselho  = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRConselho($valor) { $this->obRConselho       = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTProfissao($valor) { $this->obTProfissao      = $valor; }

//GETTERS
/**
    * @access Public
    * @param String $valor
*/
function getNomeProfissao() { return $this->stNomeProfissao;   }
/**
    * @access Public
    * @param Integer $valor
*/
function getCodigoProfissao() { return $this->inCodigoProfissao; }
/**
    * @access Public
    * @param Integer $valor
*/
function getCodigoConselho() { return $this->inCodigoConselho;  }
/**
    * @access Public
    * @param Integer $valor
*/
function getRConselho() { return $this->obRConselho;       }
/**
    * @access Public
    * @param Object $valor
*/
function getTProfissao() { return $this->obTProfissao;      }
/**
    * @access Private
*/
function RProfissao()
{
    $this->setRConselho  ( new RConselho  );
    $this->setTProfissao ( new TProfissao );
}
/**
    * Metodo de Inclusao de Profissao
    * @access Public
    * @return $obErro boolean
*/
function incluirProfissao($boTransacao = "")
{
    $obErro = $this->obTProfissao->proximoCod( $inCodProfissao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->setCodigoConselho( $inCodProfissao );
        $this->obTProfissao->setDado( "cod_profissao", $inCodProfissao            );
        $this->obTProfissao->setDado( "nom_profissao", $this->getNomeProfissao()  );
        $this->obTProfissao->setDado( "cod_conselho" , $this->getCodigoConselho() );
        $obErro = $this->obTProfissao->inclusao( $boTransacao );
    }

    return $obErro;
}
/**
    * Metodo de Alteracao de Profissao
    * @access Public
    * @return $obErro boolean
*/

function alterarProfissao($boTransacao = "")
{
    $this->obTProfissao->setDado( "cod_profissao", $this->getCodigoProfissao() );
    $this->obTProfissao->setDado( "nom_profissao", $this->getNomeProfissao()   );
    $this->obTProfissao->setDado( "cod_conselho" , $this->getCodigoConselho()  );
    $obErro = $this->obTProfissao->alteracao( $boTransacao );

    return $obErro;
}
/**
    * Metodo de Exclusao de Profissao
    * @access Public
    * @return $obErro boolean
*/

function excluirProfissao($boTransacao = "")
{
    $this->obTProfissao->setDado( "cod_profissao", $this->getCodigoProfissao() );
    $obErro = $this->obTProfissao->exclusao( $boTransacao );

    return $obErro;
}
/**
    * Metodo de consulta Profissao
    * @access Public
    * @return $obErro boolean
*/

function consultarProfissao($boTransacao = "")
{
    $this->obTProfissao->setDado( "cod_profissao", $this->getCodigoProfissao() );
    $obErro = $this->obTProfissao->recuperaPorChave( $rsListaProfissao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->setNomeProfissao ( $rsListaProfissao->getCampo("nom_profissao") );
        $this->setCodigoConselho( $rsListaProfissao->getCampo("cod_conselho")  );
        $this->obRConselho->setCodigoConselho( $rsListaProfissao->getCampo("cod_conselho")  );
        $obErro = $this->obRConselho->consultarConselho( $boTransacao );
    }

    return $obErro;
}
/**
    * Metodo de Listagem, retorna um recordset preenchido com as profissoes cadastradas
    * @access Public
    * @return $obErro boolean
*/

function listarProfissao(&$rsListaProfissao, $boTransacao = "")
{
    $stOrdem = " ORDER BY nom_profissao ";
    $obErro = $this->obTProfissao->recuperaTodos( $rsListaProfissao,"" ,$stOrdem ,$boTransacao );

    return $obErro;
}
/**
    * Metodo de listagem, retorna lista com profissoes contabeis
    * @access Public
    * @return $obErro boolean
*/

function listarProfissaoContabil(&$rsListaProfissao, $boTransacao = "")
{
    $stFiltro  = " WHERE cod_profissao IN ( ";
    $stFiltro .= " SELECT valor::integer FROM administracao.configuracao ";
    $stFiltro .= " WHERE ( parametro = 'cod_contador' ";
    $stFiltro .= " or parametro = 'cod_tec_contabil') ";
    $stFiltro .= " and exercicio='".Sessao::getExercicio()."')";
    $stOrdem  = " ORDER BY nom_profissao ";
    $obErro   = $this->obTProfissao->recuperaTodos( $rsListaProfissao, $stFiltro ,$stOrdem ,$boTransacao );

    return $obErro;
}
/**
    * Metodo que retorna recordset preenchido com Profissoes selecionadas de acordo com parametros da interface
    * @access Public
    * @return $obErro boolean
*/
function recuperaProfissaoSelecionadas(&$rsRecordSet, $boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTModalidadeClassificacaoObra->setDado( "cod_classificacao", $this->inCodClassificacao );
        $obErro = $this->obTModalidadeClassificacaoObra->recuperaDocumentoSelecionados( $rsRecordSet, $stFiltro,"", $boTransacao );
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro);
    }

    return $obErro;
}
/**
    * Metodo de Profissoes Disponiveis pela classificação
    * @access Public
    * @return $obErro boolean
*/

function recuperaProfissaoDisponiveis(&$rsRecordSet, $boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTModalidadeClassificacaoObra->setDado( "cod_classificacao", $this->inCodClassificacao );
        $obErro = $this->obTModalidadeClassificacaoObra->recuperaDocumentoDisponiveis( $rsRecordSet, $stFiltro,"", $boTransacao );
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro);
    }

    return $obErro;
}

}
