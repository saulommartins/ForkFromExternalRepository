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
    * Pagina oculta do Demonstrativo de Despesa - Anexo 11

    * Data de Criação: 26/02/2009

    * @author Analista: Tonismar R. Bernardo
    * @author Desenvolvedor: Lucas Andrades Mendes

    * @ignore

    $Id:$

    * Casos de uso: uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

switch ($_REQUEST['stCtrl']) {
    case 'Periodicidade':

       $inCodPeriodicidade =  $_REQUEST['inCodTipoPeriodicidade'];

        if ($_REQUEST['inCodTipoPeriodicidade']) {

            $obCmbPeriodo  = new Select;
            $obCmbPeriodo->setName("inCodPeriodo");
            $obCmbPeriodo->setRotulo("*Periodo");
            $obCmbPeriodo->addOption("","Selecione");
            $obCmbPeriodo->obEvento->setOnChange("jq('#stPeriodo').val(this[this.selectedIndex].text);;");

            if ($inCodPeriodicidade == 1) {
               $obCmbPeriodo->addOption("1","Janeiro");
               $obCmbPeriodo->addOption("2","Fevereiro");
               $obCmbPeriodo->addOption("3","Março");
               $obCmbPeriodo->addOption("4","Abril");
               $obCmbPeriodo->addOption("5","Maio");
               $obCmbPeriodo->addOption("6","Junho");
               $obCmbPeriodo->addOption("7","Julho");
               $obCmbPeriodo->addOption("8","Agosto");
               $obCmbPeriodo->addOption("9","Setembro");
               $obCmbPeriodo->addOption("10","Outubro");
               $obCmbPeriodo->addOption("11","Novembro");
               $obCmbPeriodo->addOption("12","Dezembro");
            }

            if ($inCodPeriodicidade == 2) {
               $obCmbPeriodo->addOption("13","1º Bimestre");
               $obCmbPeriodo->addOption("14","2º Bimestre");
               $obCmbPeriodo->addOption("15","3º Bimestre");
               $obCmbPeriodo->addOption("16","4º Bimestre");
               $obCmbPeriodo->addOption("17","5º Bimestre");
               $obCmbPeriodo->addOption("18","6º Bimestre");

            }

            if ($inCodPeriodicidade == 3) {
               $obCmbPeriodo->addOption("19","1º Quadrimestre");
               $obCmbPeriodo->addOption("20","2º Quadrimestre");
               $obCmbPeriodo->addOption("21","3º Quadrimestre");
            }

            if ($inCodPeriodicidade == 4) {
               $obCmbPeriodo->addOption("22","1º Semestre");
               $obCmbPeriodo->addOption("23","2º Semestre");

            }

            $obForm = new Formulario;
            $obForm->addComponente($obCmbPeriodo);
            $obForm->montaInnerHTML();

            $stHTML = $obForm->getHTML ();

            $stJs = "d.getElementById('spnPeriodo').innerHTML = ' ".$stHTML."'";

          break;
    }

}

SistemaLegado::executaFrameOculto($stJs);

?>
