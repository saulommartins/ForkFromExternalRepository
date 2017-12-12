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
 * Oculto do Recibo Receita Extra
 *
 * @category   Urbem
 * @package    Framework
 * @author     Analista Tonismar Bernardo <tonismar.bernardo@cnm.org.br>
 * @author     Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';
include CAM_GF_TES_MAPEAMENTO.'TTesourariaTransferenciaEstornada.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ReciboReceitaExtra";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

switch ($stCtrl) {
case 'montaEstornos':

    $obTTransferenciaEstornada = new TTesourariaTransferenciaEstornada();
    $obTTransferenciaEstornada->setDado('exercicio'   ,$_GET['exercicio']   );
    $obTTransferenciaEstornada->setDado('cod_entidade',$_GET['cod_entidade']);
    $obTTransferenciaEstornada->setDado('cod_lote'    ,$_GET['cod_lote']    );
    $obTTransferenciaEstornada->setDado('tipo'        ,$_GET['tipo']        );
    $obTTransferenciaEstornada->recuperaTransferenciaEstornada($rsEstornos);

    $rsEstornos->addFormatacao('valor','NUMERIC_BR');

    //Instancia uma Table para demonstrar os estornos
    $obTable = new Table             ();
    $obTable->setRecordset            ($rsEstornos);
    $obTable->setSummary              ('Estornos');
    //$obTable->setConditional          (true , '#efefef');
    $obTable->setSummary              ('Lista de Estornos');

    $obTable->Head->addCabecalho      ('Data',10);
    $obTable->Head->addCabecalho      ('Valor',10);

    $obTable->Body->addCampo          ('dt_estorno','C');
    $obTable->Body->addCampo          ('valor','D');

    $obTable->montaHTML               ();
    echo $obTable->getHTML();

    break;

case 'preencheDataEmissao':
        include_once CAM_GF_TES_MAPEAMENTO.'TTesourariaReciboExtra.class.php';
        $obTReciboExtra = new TTesourariaReciboExtra;
        /////pegando a data do ultimo recibo de Receita
        $obTReciboExtra->setDado ('tipo_recibo','R');
        $obTReciboExtra->setDado ('exercicio',Sessao::getExercicio());
        $obTReciboExtra->setDado ('cod_entidade',$_REQUEST['inCodEntidade']);
        $obTReciboExtra->recuperaUltimaDataRecibo( $rsDataRecibo );

        if ( $rsDataRecibo->getCampo( 'data' ) ) {
           $stUltimaData = substr($rsDataRecibo->getCampo( 'data' ), 0, 10 );
           $stUltimaData = explode (  '-', $stUltimaData );
           $stUltimaData = $stUltimaData[2].'/'.$stUltimaData[1].'/'.$stUltimaData[0];
           $stJs .= "d.getElementById('dtDataEmissao').value = '".$stUltimaData."';\n"; 
        }else {
            $stJs .= "d.getElementById('dtDataEmissao').value = '';\n"; 
        }
        
       SistemaLegado::executaFrameOculto($stJs);
    break;
}
?>
