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
    * Classe de regra de negócio
    * Data de Criação   : 30/06/2005

    * @author Analista: Dieine da Silva
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.05
*/

/*
$Log$
Revision 1.7  2006/07/05 20:42:11  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO );
include_once( CAM_FW_PDF."RRelatorio.class.php"               );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRecurso.class.php"                 );

/**
    * Classe de regra de negócio

    * @author Analista: Dieine da Silva
    * @author Desenvolvedor: Anderson R. M. Buzo
*/
class ROrcamentoRelatorioRecurso
{
/**
    * @var Object
    * @access Private
*/
var $obROrcamentoRecurso;
/**
    * @var String
    * @access Private
*/
var $stExercicio;

/**
     * @access Public
     * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio  = $valor; }

/**
     * @access Public
     * @return String
*/
function getExercicio() { return $this->stExercicio;            }

/**
    * Método Construtor
    * @access Private
*/
function ROrcamentoRelatorioRecurso()
{
    $this->obRRelatorio        = new RRelatorio;
    $this->obROrcamentoRecurso = new ROrcamentoRecurso;
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordSet , $stOrder = "")
{
    $this->obROrcamentoRecurso->setExercicio( $this->stExercicio );
    $obErro = $this->obROrcamentoRecurso->listar( $rsRecordSet , 'cod_recurso,nom_recurso' );
    if ( !$obErro->ocorreu() ) {

        $inCount = 0;
        while ( !$rsRecordSet->eof() ) {
            $arRecordSet[$inCount]['cod_recurso'] = $rsRecordSet->getCampo( "cod_recurso" );

            $inCountRecurso = 0;
            $stNomRecurso = str_replace( chr(10), "", $rsRecordSet->getCampo( "nom_recurso" ) );
            $stNomRecurso = wordwrap( $stNomRecurso, 50, chr(13) );
            $arNomRecurso = explode( chr(13), $stNomRecurso );
            foreach ($arNomRecurso as $stNomRecurso) {
                $arRecordSet[$inCount]['nom_recurso'] = $stNomRecurso;
                $inCount++;
                $inCountRecurso++;
            }
            $inCount -= $inCountRecurso;

            $inCountFinalidade = 0;
            $stFinalidade = str_replace( chr(10), "", $rsRecordSet->getCampo( "finalidade" ) );
            $stFinalidade = wordwrap( $stFinalidade, 92, chr(13) );
            $arFinalidade = explode( chr(13), $stFinalidade );
            foreach ($arFinalidade as $stFinalidade) {
                $arRecordSet[$inCount]['finalidade'] = $stFinalidade;
                $inCount++;
                $inCountFinalidade++;
            }
            $inCount -= $inCountFinalidade;

            $inCount = ( $inCountRecurso > $inCountFinalidade ) ? $inCount + $inCountRecurso : $inCount + $inCountFinalidade;

            $rsRecordSet->proximo();
        }

        $rsRecordSet = new RecordSet;
        $rsRecordSet->preenche( $arRecordSet );

    }

    return $obErro;
}

}
