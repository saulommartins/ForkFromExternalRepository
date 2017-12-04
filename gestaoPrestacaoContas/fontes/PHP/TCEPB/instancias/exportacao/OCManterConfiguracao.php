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
    * Pagina Oculta para Formulário
    * Data de Criação   : 24/01/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    $Revision: 59612 $
    $Name$
    $Autor:$
    $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $

    * Casos de uso: uc-06.03.00

*/

/*
$Log$
Revision 1.2  2007/04/23 15:40:02  rodrigo_sr
uc-06.03.00

Revision 1.1  2007/01/25 20:39:47  diego
Novos arquivos de exportação.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $_REQUEST['stCtrl'] ?  $_REQUEST['stCtrl'] : $_REQUEST['stCtrl'];
$arFiltro = Sessao::read('filtro');
switch ($stCtrl) {
    case "BuscaCodigo":
          if ($_REQUEST['inCodEntidade'] != '') {
            include_once(TADM."TAdministracaoConfiguracaoEntidade.class.php");

            $obPersistente = new TAdministracaoConfiguracaoEntidade();
            $obPersistente->setDado('exercicio'     ,Sessao::getExercicio());
            $obPersistente->setDado('cod_entidade'  ,$_REQUEST['inCodEntidade']);
            $obPersistente->setDado('cod_modulo'    ,8);
            $obPersistente->setDado('parametro'     ,'tcepb_codigo_unidade_gestora');

            $obTLicitacaoPreEmpenho = new TLicitacaoLicitacaoPreEmpenho();
            $arEmpenho = explode('/',$_REQUEST['inCodEmpenho']);
            $arEmpenho[1] = $arEmpenho[1] == '' ? '2006' : $arEmpenho[1];
            $obTLicitacaoPreEmpenho->setDado('cod_empenho',$arEmpenho[0]);
            $obTLicitacaoPreEmpenho->setDado('exercicio',$arEmpenho[1]);
            $obTLicitacaoPreEmpenho->setDado('cod_entidade',$arFiltro['cod_entidade']);

            $obTLicitacaoPreEmpenho->recuperaLicitacaoPorEmpenho($rsLicitacaoEmpenho);

            if ($rsLicitacaoEmpenho->getCampo('cod_licitacao') != '') {

                $obForm = new Form();
                $obLblNumeroLicitacao = new ILabelNumeroLicitacao( $obForm );
                $obLblNumeroLicitacao->setMostrarObjeto( true );
                $obLblNumeroLicitacao->setExercicio( $rsLicitacaoEmpenho->getCampo('exercicio')  );
                $obLblNumeroLicitacao->setNumLicitacao( $rsLicitacaoEmpenho->getCampo('cod_licitacao') );

                include_once( TCOM."TComprasOrdemCompra.class.php" );
                $obTComprasOrdemCompra = new TComprasOrdemCompra();
                $obTComprasOrdemCompra->setDado('cod_entidade',$arFiltro['cod_entidade']);
                $obTComprasOrdemCompra->setDado('cod_empenho',$arEmpenho[0]);
                $obTComprasOrdemCompra->setDado('exercicio',$arEmpenho[1]);

                $obTComprasOrdemCompra->recuperaFornecedorOrdemCompra($rsFornecedor);
                $obTComprasOrdemCompra->recuperaItensOrdemCompra($rsItens);

                $obLblFornecedor = new Label();
                $obLblFornecedor->setRotulo("Fornecedor");
                $obLblFornecedor->setValue($rsFornecedor->getCampo('cgm_beneficiario')." - ".$rsFornecedor->getCampo('nom_cgm'));

                $obFormulario = new Formulario($obForm);
                $obLblNumeroLicitacao->geraFormulario( $obFormulario );
                $obFormulario->addComponente($obLblFornecedor);
                $obFormulario->montaInnerHTML();
                $stHTML = $obFormulario->getHTML();
                $stJs .= "d.getElementById('spnPreEmpenho').innerHTML = '".$stHTML."';\n";

                $inCount = 0;
                $arItens = Sessao::read('arItens');
                while (!$rsItens->eof()) {
                    $arItens[$inCount]['nom_item'] = $rsItens->getCampo('nom_item');
                    $arItens[$inCount]['centro_custo'] = $rsItens->getCampo('centro_custo');
                    $arItens[$inCount]['qtd_empenho']  = number_format($rsItens->getCampo('quantidade_emp'),2);
                    $arItens[$inCount]['vl_unitario'] = bcdiv($rsItens->getCampo('vl_total'),$rsItens->getCampo('quantidade_emp'),2);
                    $arItens[$inCount]['qtd_oc']= bcsub($rsItens->getCampo('quantidade_emp') ,(bcadd($rsItens->getCampo('quantidade_oc'),$rsItens->getCampo('quantidade_oc_anulada'),2)),2);
                    $arItens[$inCount]['vl_oc'] = bcmul($arItens['arItens'][$inCount]['qtd_oc'],$arItens['arItens'][$inCount]['vl_unitario'],2);
                    $arItens[$inCount]['qtd_saldo'] = bcsub($rsItens->getCampo('quantidade_emp'),$arItens['arItens'][$inCount]['qtd_oc'],2);
                    $arItens[$inCount]['num_item'] = $rsItens->getCampo('num_item');
                    $arItens[$inCount]['cod_pre_empenho'] = $rsItens->getCampo('cod_pre_empenho');
                    $inCount++;

                    $rsItens->proximo();
                }
            } else {
                $arItens = array();
            }
            Sessao::write('arItens',$arItens);
            $stJs .= montaListaItens( $arItens );
        }

    break;
}
echo $stJs;
?>
