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
 * Página Oculta de Lançar Metas Fisicas Realizadas.
 * Data de Criacao: 12/04/2016

 * @author Analista : Valtair Santos
 * @author Desenvolvedor : Michel Teixeira
 * @ignore

 $Id: OCManterMetasFisicas.php 65085 2016-04-22 13:41:21Z michel $

**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';

# Define o nome dos arquivos PHP
$stPrograma = 'ManterMetasFisicas';
$pgFilt     = 'FL'.$stPrograma.'.php';
$pgList     = 'LS'.$stPrograma.'.php';
$pgForm     = 'FM'.$stPrograma.'.php';
$pgProc     = 'PR'.$stPrograma.'.php';
$pgOcul     = 'OC'.$stPrograma.'.php';
$pgJs       = 'JS'.$stPrograma.'.js';

$stCtrl = $request->get('stCtrl');
$stAcao = $request->get('stAcao');

function montaMetaFisica(Request $request)
{
    $inCodAcao             = $request->get('cod_acao');
    $inCodRecurso          = $request->get('cod_recurso');
    $stJsMetaFisicaRecurso = "";
    $rsRecordSet           = new RecordSet;
    $arParametrosMetas     = Sessao::read('arParametrosMetas');
    $arRecursoAno          = $arParametrosMetas[$inCodAcao.'.'.$inCodRecurso];

    for ($i = 1; $i < 5; $i++) {
        $rsRecordSet->add( array('ano'             => $i,
                                 'descricao'       => '<strong>Ano '.$i.' do PPA</strong>',
                                 'quantidade'      => number_format($arRecursoAno['flQuantidade_'.$i],2,',','.'),
                                 'valor'           => number_format($arRecursoAno['flValorTotal_'.$i],2,',','.'),
                                 'valor_realizado' => number_format($arRecursoAno['flValorRealizado_'.$i],2,',','.'),
                                 'justificativa'   => $arRecursoAno['stJustificativa_'.$i]
                                )
                         );

        $stIdVl = 'flValorRealizado_'.$i.'_'.$inCodAcao.'_'.$inCodRecurso;
        $stIdJt = 'stJustificativa_'.$i.'_'.$inCodAcao.'_'.$inCodRecurso;

        if($arRecursoAno['stExercicio_'.$i] <= Sessao::getExercicio()){
            $inAno = $i;

            $stJsMetaFisicaRecurso .= "setLabel('".$stIdVl."', true); ";
            $stJsMetaFisicaRecurso .= "setLabel('".$stIdJt."', true); ";
            $stJsMetaFisicaRecurso .= "jq('#".$stIdVl."').removeAttr('style').css('text-align', 'right');";
        }

        //Substituindo espaço padrão do textarea
        if(empty($arRecursoAno['stJustificativa_'.$i]) || htmlentities($arRecursoAno['stJustificativa_'.$i])=='&nbsp;')
            $stJsMetaFisicaRecurso .= "jq('#".$stIdJt."').val('');jq('#".$stIdJt."').html('');";
    }

    $obTxtQuantidade = new Numerico;
    $obTxtQuantidade->setId('flQuantidade_[ano]_'.$inCodAcao.'_'.$inCodRecurso);
    $obTxtQuantidade->setName('flQuantidade_[ano]_'.$inCodAcao.'_'.$inCodRecurso);
    $obTxtQuantidade->setLabel(true);
    $obTxtQuantidade->setClass('valor');
    $obTxtQuantidade->setValue('[quantidade]');
    $obTxtQuantidade->setMaxLength(14);
    $obTxtQuantidade->setSize(14);
    $obTxtQuantidade->setStyle('text-align:right');
    $obTxtQuantidade->setNegativo(false);

    $obTxtValorPrevisto = new Numerico;
    $obTxtValorPrevisto->setId('flValor_[ano]_'.$inCodAcao.'_'.$inCodRecurso);
    $obTxtValorPrevisto->setName('flValor_[ano]_'.$inCodAcao.'_'.$inCodRecurso);
    $obTxtValorPrevisto->setLabel(true);
    $obTxtValorPrevisto->setClass('valor');
    $obTxtValorPrevisto->setValue('[valor]');
    $obTxtValorPrevisto->setMaxLength(14);
    $obTxtValorPrevisto->setSize(14);

    $obTxtValorRealizado= new Numerico;
    $obTxtValorRealizado->setId('flValorRealizado_[ano]_'.$inCodAcao.'_'.$inCodRecurso);
    $obTxtValorRealizado->setName('flValorRealizado_[ano]_'.$inCodAcao.'_'.$inCodRecurso);
    $obTxtValorRealizado->setLabel(true);
    $obTxtValorRealizado->setClass('valor');
    $obTxtValorRealizado->setValue('[valor_realizado]');
    $obTxtValorRealizado->setMaxLength(14);
    $obTxtValorRealizado->setSize(25);
    $obTxtValorRealizado->setStyle('text-align:right');
    $obTxtValorRealizado->setNegativo(false);

    $obTxtJustificativa = new TextArea;
    $obTxtJustificativa->setName('stJustificativa_[ano]_'.$inCodAcao.'_'.$inCodRecurso);
    $obTxtJustificativa->setId('stJustificativa_[ano]_'.$inCodAcao.'_'.$inCodRecurso);
    $obTxtJustificativa->setValue('[justificativa]');
    $obTxtJustificativa->setMaxCaracteres(254);
    $obTxtJustificativa->setLabel(true);
    $obTxtJustificativa->setCols( 30 );
    $obTxtJustificativa->setRows( 1 );

    $stNomBtn = "btnAlterar_".$inCodAcao.'_'.$inCodRecurso.'_'.$inAno;
    $obBtnAlterar = new Button;
    $obBtnAlterar->setName  ($stNomBtn);
    $obBtnAlterar->setId    ($stNomBtn);
    $obBtnAlterar->setValue ("Alterar");
    $obBtnAlterar->setTipo  ("button");
    $obBtnAlterar->obEvento->setOnClick("buscaValor('alterarMetaFisica', '&stId=".$inAno.'_'.$inCodAcao.'_'.$inCodRecurso."','".$request->get('linha_table_tree')."');");
    $obBtnAlterar->montaHTML();

    $obTable = new Table;
    $obTable->setId('tblMetaFisica_'.$inCodAcao.'_'.$inCodRecurso);
    $obTable->setRecordset      ($rsRecordSet);
    $obTable->setTitle          ('Metas Físicas Realizadas');
    $obTable->setSummary        ('Metas Físicas Realizadas');
    $obTable->setLineNumber     (false);

    $obTable->Head->addCabecalho('&nbsp;&nbsp;'   , 20);
    $obTable->Head->addCabecalho('Quantidade'     , 13);
    $obTable->Head->addCabecalho('Valor Previsto' , 13);
    $obTable->Head->addCabecalho('Valor Realizado', 13);
    $obTable->Head->addCabecalho('Justificativa'  , 30);

    $obTable->Body->addCampo    ('descricao'         , 'C');
    $obTable->Body->addCampo    ($obTxtQuantidade    , 'R');
    $obTable->Body->addCampo    ($obTxtValorPrevisto , 'R');
    $obTable->Body->addCampo    ($obTxtValorRealizado, 'R');
    $obTable->Body->addCampo    ($obTxtJustificativa , 'R');

    $obTable->montaHTML();

    return $obTable->getHtml().$obBtnAlterar->getHtml()."<script type='text/javascript> ".$stJsMetaFisicaRecurso." </script>";
}

$stJs = "";

switch ($stCtrl) {
    case 'montaMetaFisica':
        $stJs .= montaMetaFisica($request);
    break;

    case 'alterarMetaFisica':
        $arParametrosMetas = Sessao::read('arParametrosMetas');

        $stId = $request->get('stId');
        list($inAno, $inCodAcao, $inCodRecurso) = explode('_', $stId);

        $stNumAcao = $arParametrosMetas[$inCodAcao.'.'.$inCodRecurso]['num_acao'];
        $stRecurso = $arParametrosMetas[$inCodAcao.'.'.$inCodRecurso]['nom_cod_recurso'];

        for ($i = 1; $i <= $inAno; $i++) {
            $flValor = $request->get('flValorRealizado_'.$i.'_'.$inCodAcao.'_'.$inCodRecurso);
            $stJustificativa = $request->get('stJustificativa_'.$i.'_'.$inCodAcao.'_'.$inCodRecurso);

            $arParametrosMetas[$inCodAcao.'.'.$inCodRecurso]['flValorRealizado_'.$i] = str_replace(',','.',str_replace('.','',$flValor));
            $arParametrosMetas[$inCodAcao.'.'.$inCodRecurso]['stJustificativa_'.$i] = trim($stJustificativa);
        }

        $arParametrosMetas[$inCodAcao.'.'.$inCodRecurso]['boAlterado'] = TRUE;

        Sessao::write('arParametrosMetas', $arParametrosMetas);

        $js = "alertaAviso('@Atualizado Ação: ".$stNumAcao." e Recurso: ".$stRecurso." ','form','erro','".Sessao::getId()."'); \n";
    break;
}

echo $stJs;

if(isset($js))
    sistemalegado::executaFrameOculto($js);

?>
