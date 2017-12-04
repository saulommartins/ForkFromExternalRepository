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
    * Data de Criação: 10/12/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * $Id: OCRelatorioAbastecimento.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.02.18
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

switch ($_REQUEST['stCtrl']) {
    case 'montaPeriodo' :
        if ($_REQUEST['slTipoRelatorio'] != '') {
            if ($_REQUEST['slTipoRelatorio'] != 3) {

                //cria um componente periodicidade
                $obPeriodicidade = new Periodicidade();
                $obPeriodicidade->setExercicio(date('Y'));
                $obPeriodicidade->setExibeDia( false );
                $obPeriodicidade->setNull( false );

                $obFormulario = new Formulario();
                $obFormulario->addComponente( $obPeriodicidade );
                $obFormulario->montaInnerHTML();

                $stJs .= "$('spnPeriodo').innerHTML = '".$obFormulario->getHTML()."';";
            } else { $stJs .= "$('spnPeriodo').innerHTML = '';"; }
        } else { $stJs .= "$('spnPeriodo').innerHTML = '';"; }

        break;
}
echo $stJs;
