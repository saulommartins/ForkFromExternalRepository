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
    * Página de processamento para emissão do Mapa de Compras
    * Data de criação : 23/10/2008

    * @author Analista: Gelson W. Gonçalves
    * @author Programador: Diogo Zarpelon

    * @ignore

    $Id:$

**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

if ($request->get('boEmitirMapa')) {
    $obBirtPreview = new PreviewBirt(3, 35, 4);
    $obBirtPreview->setVersaoBirt( "2.5.0");
    $obBirtPreview->setExportaExcel (true);

    $obBirtPreview->setTitulo("Emissão do Mapa de Compra");

    $obBirtPreview->addParametro( "codMapaCompra"         , $_REQUEST['inCodMapa']       );
    $obBirtPreview->addParametro( "stExercicioMapaCompra" , $_REQUEST['stExercicioMapa'] );
    $obBirtPreview->addParametro( "boMostraDado"          , $_REQUEST['boMostraDado']    );

    # Quebra a string de data para separar data e hora.
    if (!empty($_REQUEST['stDataMapa'])) {
        list($stDataMapa, $stHoraMapa) = explode(" ", $_REQUEST['stDataMapa']);
        $obBirtPreview->addParametro( "stDataMapa" , $stDataMapa);
        $obBirtPreview->addParametro( "stHoraMapa" , $stHoraMapa);
    }

    $obBirtPreview->preview();
} else {
    $arLink = Sessao::read('link');
    $pgList = "LSManterMapaCompras.php?".Sessao::getId()."&stAcao=".$arLink["stAcao"];
    $stMensagem = "Redirecionando para a consulta de Mapa.";

    sistemaLegado::alertaAviso($pgList,$stMensagem ,"incluir","aviso", Sessao::getId(), "");
}
