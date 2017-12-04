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
     * 
    * Data de Criação   : 26/09/2014
    * @author Analista:
    * @author Desenvolvedor:  Jean Felipe da Silva
    * @ignore
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once ( CAM_GA_NORMAS_COMPONENTES."IBuscaInnerNorma.class.php" );
include_once ( CAM_GRH_PES_MAPEAMENTO."TTPessoalTCMBAAssentamentoAtoPessoal.class.php" );

$stPrograma = "ManterRelacionarAtosPessoal";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stCtrl = $request->get('stCtrl');

$arLista = Sessao::read('arLista');

function montaLista ($arLista)
{
    $rsLista = new RecordSet();
    
    foreach ($arLista as $value){
        if ($value['excluido'] == 'n'){
            $arTemp[] = $value;
        }
    }
    
    $rsLista->preenche( $arTemp );
    
    $obTable = new Table();
    $obTable->setRecordSet( $rsLista );
    $obTable->setSummary('Lista de Históricos Funcionais Relacionados');

    $obTable->Head->addCabecalho( 'Tipo de Ato Pessoal' , 15 );
    $obTable->Head->addCabecalho( 'Assentamento' , 15 );

    $obTable->Body->addCampo( 'desc_ato', 'C' );
    $obTable->Body->addCampo( 'desc_assentamento', 'C' );

    $obTable->Body->addAcao( 'excluir' ,  'excluirListaItens(%s)', array( 'id' ) );

    $obTable->montaHTML();
    $stHTML = $obTable->getHtml();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );
    
    $stJs = "jq('#spnLista').html('".$stHTML."');\n";
    $stJs.= "jq('#inCodTipoAto').val('');\n";
    $stJs.= "jq('#inCodAssentamento').val('');\n";

    return $stJs;
}

switch ($stCtrl) {
    case 'incluiLista':
        $arLista = Sessao::read('arLista');
        $arAssentamento = explode('_', $request->get('inCodAssentamento'));
      
        if (is_array($arLista)) {
            foreach ($arLista as $arListaTmp) {
                if ($arListaTmp['inCodTipoAto'] == $request->get('inCodTipoAto') AND $arListaTmp['inCodAssentamento'] == $arAssentamento[0]) {
                    echo "alertaAviso('@Tipo de Assentamento já está na Lista de Históricos Funcionais.','form','erro','".Sessao::getId()."');";
                    exit;
                }
            }
        }
        
        if($request->get('inCodTipoAto') == ''){
            echo "alertaAviso('@O campo Tipo de Ato Pessoal é obrigatório.','form','erro','".Sessao::getId()."');";
            exit;
        }
        
        if($request->get('inCodAssentamento') == ''){
            echo "alertaAviso('@O campo Assentamento é obrigatório.','form','erro','".Sessao::getId()."');";
            exit;            
        }
        
        $arTmp['id']                = count($arLista);
        $arTmp['inCodTipoAto']      = $request->get('inCodTipoAto');
        $arTmp['inCodAssentamento'] = $arAssentamento[0];
        $arTmp['desc_ato']          = SistemaLegado::pegaDado('descricao', 'tcmba.tipo_ato_pessoal',  'WHERE cod_tipo ='.$request->get('inCodTipoAto'));
        $arTmp['desc_assentamento'] = SistemaLegado::pegaDado('descricao', 'pessoal'.Sessao::getEntidade().'.assentamento_assentamento', 'WHERE cod_assentamento ='.$arAssentamento[0]);
        $arTmp['excluido']          = 'n';
            
        $arLista[] = $arTmp;
        
        Sessao::write('arLista', $arLista);
        echo montaLista($arLista);
        
    break;

    case 'excluirListaItens':
        $arLista   = array();
        $arTemp = Sessao::read('arLista');
        $inCount = 0;
        
        foreach ($arTemp as $key => $value) {
            $arLista[$inCount]['id']                    = $inCount;
            $arLista[$inCount]['inCodTipoAto']          = $value['inCodTipoAto'];
            $arLista[$inCount]['inCodAssentamento']     = $value['inCodAssentamento'];
            $arLista[$inCount]['desc_assentamento']     = $value['desc_assentamento'];
            $arLista[$inCount]['desc_ato']              = $value['desc_ato'];
            
            if ($value['id'] != $request->get('id')) {
                $arLista[$inCount]['excluido']      = $value['excluido'];
            } else {
                $arLista[$inCount]['excluido']      = 's';
            }
            
            $inCount++;
        }
        Sessao::write('arLista', $arLista);
        echo montaLista( $arLista );
        
    break;

    case 'montaLista':
        $inCount = 0;
        $obTTPessoalTCMBAAssentamentoAtoPessoal = new TTPessoalTCMBAAssentamentoAtoPessoal();
        $obTTPessoalTCMBAAssentamentoAtoPessoal->recuperaTodos($rsLista, " WHERE tcmba_assentamento_ato_pessoal.exercicio = '".Sessao::getExercicio()."'");

        foreach ($rsLista->getElementos() AS $arValue) {
            $arLista[$inCount]['id']                = $inCount;
            $arLista[$inCount]['inCodTipoAto']      = $arValue['cod_tipo_ato_pessoal'];
            $arLista[$inCount]['inCodAssentamento'] = $arValue['cod_assentamento'];
            $arLista[$inCount]['desc_ato']          = SistemaLegado::pegaDado('descricao', 'tcmba.tipo_ato_pessoal', 'WHERE cod_tipo = '.$arValue['cod_tipo_ato_pessoal']);
            $arLista[$inCount]['desc_assentamento'] = SistemaLegado::pegaDado('descricao', 'pessoal'.Sessao::getEntidade().'.assentamento_assentamento', 'WHERE cod_assentamento ='.$arValue['cod_assentamento']);
            $arLista[$inCount]['excluido']          = 'n';
            $inCount++;
        }
        
        Sessao::write('arLista', $arLista);
        
        if (isset($arLista)){
            echo montaLista( $arLista );
        }
        
    break;

}

echo $stJs;

?>