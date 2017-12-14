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
* Gerar o componente o SelectMultiplo com os bancos
* Data de Criação: 09/11/2005

* @author Analista: Vandre Miguel Ramos
* @author Desenvolvedor: Bruce Cruz de Sena

* @package beneficios
* @subpackage componentes

Casos de uso: uc-00.00.00

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/GA.inc.php';

/**
    * Cria o componente SelectMultiplo com a Lotação
    * @author Desenvolvedor: Andre Almeida

    * @package beneficios
    * @subpackage componentes
*/
class ISelectMultiploAgencia extends SelectMultiplo
{
var $obRMonAgencia;

function setCodbanco($valor = '') { $this->stCodBanco = $valor; }
function setTMonAgencia($valor) { $this->obRMonAgencia = $valor; }

function getTMonAgencia() { return $this->obRMonAgencia; }
function getCodbanco() { return $this->stCodBanco;    }

/*
  Método Construtor
*/
function ISelectMultiploAgencia()
{
    $stFiltro = '';

    parent::SelectMultiplo();
    include_once(CAM_GT_MON_MAPEAMENTO . "TMONAgencia.class.php");

    if ( $this->getCodbanco() ) {
        $stFiltro = ' where num_banco = \''.$this->obITextBoxSelectBanco->obTextBox->getValue().'\'';
    }

//    $obTMapeamento->recuperaTodos($rsRecordSet, $stFiltro);
//    $obTMapeamento          = new TMONAgencia();

    $rsDisponiveis = new Recordset;

    $rsSelecionados = new Recordset;
    $this->setName       ( "inCodAgencia"                        );
    $this->setRotulo     ( "Agência"                             );
    $this->setTitle      ( "Selecione agência(s) para o filtro." );
    $this->setNomeLista1 ( "inCodAgenciaDisponiveis"             );
    $this->setRecord1    ( $rsDisponiveis                        );
    $this->setCampoId1   ( "[cod_agencia]"                       );
    $this->setCampoDesc1 ( "[num_agencia] - [nom_agencia]"       );
    $this->setStyle1     ( "width:300px"                         );
    $this->setNomeLista2 ( "inCodAgenciaSelecionados"            );
    $this->setRecord2    ( $rsSelecionados                       );
    $this->setCampoId2   ( "[cod_agencia]"                       );
    $this->setCampoDesc2 ( "[num_agencia] - [nom_agencia]"       );
    $this->setStyle2     ( "width:300px"                         );
}

}

?>
