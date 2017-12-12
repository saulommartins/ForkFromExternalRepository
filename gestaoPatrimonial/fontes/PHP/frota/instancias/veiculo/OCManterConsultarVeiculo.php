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
    * Data de Criação: 10/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * $Id: OCManterConsultarVeiculo.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.02.06
*/

setlocale(LC_ALL,'pt_BR');

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once ( CAM_GP_FRO_MAPEAMENTO.'TFrotaVeiculoDocumento.class.php' );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConsultarVeiculo";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCtrl = $_REQUEST['stCtrl'];

function montaListaDocumentos($rsDocumentos)
{
    global $pgOcul;

    $obTable = new TableTree();
    $obTable->setRecordset( $rsDocumentos );

    $obTable->setArquivo( $pgOcul );
    $obTable->setParametros( array('id') );
    $obTable->setComplementoParametros( 'stCtrl=montaEmpenho' );

    $obTable->addCondicionalTree( 'situacao','Pago' );

    $obTable->setSummary( 'Lista de Documentos do Veículo' );

    $obTable->Head->addCabecalho( 'Documento', 60 );
    $obTable->Head->addCabecalho( 'Vencimento', 10 );
    $obTable->Head->addCabecalho( 'Situação', 10 );

    $obTable->Body->addCampo( 'nom_documento', 'E' );
    $obTable->Body->addCampo( '[desc_mes]/[exercicio]', 'C' );
    $obTable->Body->addCampo( 'situacao', 'C' );

    $obTable->montaHTML( true );

    return "$('spnDocumentos').innerHTML = '".$obTable->getHtml()."';";

}

switch ($stCtrl) {
   case 'montaListaDocumentos' :
        $arDocumentos = array();
        //recupera todos os documentos do veiculo
        $obTFrotaVeiculoDocumento = new TFrotaVeiculoDocumento();
        $obTFrotaVeiculoDocumento->setDado('cod_veiculo',$_REQUEST['inCodVeiculo']);
        $obTFrotaVeiculoDocumento->recuperaDocumentos( $rsDocumentos );
        $inCount = 0;
        foreach ($rsDocumentos->arElementos as $arTemp) {
            $rsDocumentos->arElementos[$inCount]['desc_mes'] = strftime('%B',strtotime($arTemp['mes'].'/01/'.$arTemp['exercicio'])) ;
            $rsDocumentos->arElementos[$inCount]['situacao'] = ( $arTemp['situacao'] == 'naopago') ? 'Não Pago' : 'Pago';
            $rsDocumentos->arElementos[$inCount]['id'      ] = $inCount;
            $arDocumentos[$inCount] = $arTemp;
            $inCount++;
        }
        Sessao::write('arDocumentos' , $arDocumentos);
        if ( $rsDocumentos->getNumLinhas() > 0 ) {
            $stJs = montaListaDocumentos( $rsDocumentos );
        }
        break;
    case 'montaEmpenho' :

        $arDocumentos = Sessao::read('arDocumentos');

        //label para o exercicio do empenho
        $obLblExercicio = new Label();
        $obLblExercicio->setRotulo( 'Exercício' );
        $obLblExercicio->setValue( $arDocumentos[$_REQUEST['id']]['exercicio_empenho'] );

        //label para a entidade do empenho
        $obLblEntidade = new Label();
        $obLblEntidade->setRotulo( 'Entidade' );
        $obLblEntidade->setValue( $arDocumentos[$_REQUEST['id']]['cod_entidade'].' - '.$arDocumentos[$_REQUEST['id']]['nom_entidade']);

        //label para o empenho
        $obLblEmpenho = new Label();
        $obLblEmpenho->setRotulo( 'Empenho' );
        $obLblEmpenho->setValue( $arDocumentos[$_REQUEST['id']]['cod_empenho'].' - '.$arDocumentos[$_REQUEST['id']]['nom_empenho'] );

        $obFormulario = new Formulario();
        $obFormulario->addTitulo    ( 'Dados do Pagamento' );
        $obFormulario->addComponente( $obLblExercicio );
        $obFormulario->addComponente( $obLblEntidade );
        $obFormulario->addComponente( $obLblEmpenho );

        $obFormulario->montaInnerHTML();
        $stJs = $obFormulario->getHTML();

        break;
}

echo $stJs;
