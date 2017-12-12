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
    * Classe de Regra de Negócio Itens
    * Data de Criação   : 18/08/2004

    * @author Desenvolvedor: Marcelo Boezzio Paulino

    * @package URBEM
    * @subpackage Regra

    $Revision: 30824 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.27
*/

/*
$Log$
Revision 1.6  2006/07/05 20:42:11  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO        );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"                  );
include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoClassificacaoReceita.class.php" );

/**
    * Classe de Regra de Negócio Itens
    * @author Desenvolvedor: Marcelo Boezzio Paulino
*/
class ROrcamentoRelatorioAnexo3 extends PersistenteRelatorio
{
/**
    * @var String
    * @access Private
*/
var $stExercicio;

/**
    * @var Object
    * @access Private
*/
var $obTOrcamentoClassificacaoReceita;

/**
     * @access Public
     * @param Object $valor
*/
function setTOrcamentoClassificacaoReceita($valor) { $this->obTOrcamentoClassificacaoReceita  = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio        = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function getTOrcamentoClassificacaoReceita() { return $this->obTOrcamentoClassificacaoReceita;  }
/**
     * @access Public
     * @return String
*/
function getExercicio() { return $this->stExercicio;                   }

/**
    * Método Construtor
    * @access Private
*/
function ROrcamentoRelatorioAnexo3()
{
    $this->setTOrcamentoClassificacaoReceita ( new TOrcamentoClassificacaoReceita   );
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordSet , $stOrder = "")
{
    $stOrder   = " ORDER BY mascara_classificacao";
    $this->obTOrcamentoClassificacaoReceita->setDado("stExercicio",$this->getExercicio());
    $obErro = $this->obTOrcamentoClassificacaoReceita->recuperaRelacionamentoRelatorio( $rsRecordSet, $stFiltro, $stOrder );

    return $obErro;
}
}
