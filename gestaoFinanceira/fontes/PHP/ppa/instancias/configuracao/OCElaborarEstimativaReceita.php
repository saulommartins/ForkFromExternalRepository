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
    * Página de oculto da Elaboração de Estimativa da Receita
    * Data de Criação: 07/04/2009

    * @author Analista: Tonismar Bernardo <tonismar.bernardo@cnm.org.br>
    * @author Desenvolvedor: Henrique Girardi dos Santos <henrique.santos@cnm.org.br>

    * @package      URBEM
    * @subpackage   PPA

    * $Id:$
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';
require_once CAM_GF_PPA_MAPEAMENTO."TPPAEstimativaOrcamentariaBase.class.php";
require_once CAM_GF_PPA_MAPEAMENTO."TPPAPPAEstimativaOrcamentariaBase.class.php";

//Define o nome dos arquivos PHP
$stPrograma = 'ElaborarEstimativaReceita';
$pgOcul     = 'OC'.$stPrograma.".php";
$pgProc     = 'PR'.$stPrograma.".php";
$pgForm     = 'FM'.$stPrograma.".php";
$pgJs       = 'JS'.$stPrograma.".js";

$stJs = '';
switch ($_GET['stCtrl']) {

case 'montaPorcentagemAnalitica':
    // Sempre quando for para a analítica deve trazer sempre o que há na base, não deixando os alterados na tela
    $stJs .= getJsPorcentagem();
    break;

case 'montaPorcentagemSintetica':
    // Quando for sintética, realiza a verificação, se os dados gravados da PPA forem sintéticos, então busca os dados dele na base
    // Caso não seja, os dados são limpos na tela para que não fiquem possiveis dados diferentes na tela como sendo sintético, onde todos os
    // Valores devem ser iguais
    $stJs .= getJsPorcentagem('sintetica');
    break;

case 'montaListagemReceitas':

    if ($_GET['inCodPPA'] != '') {
        $rsPPAEstimativa = getRecordSetEstimativaOrcamentaria($_GET['inCodPPA']);

        if ($rsPPAEstimativa->getNumLinhas() > 0) {

            $arPPA = $rsPPAEstimativa->getElementos();
            $stTipoPercentualInformado = $arPPA[0]['tipo_percentual_informado'];

            $stJs .= 'jq("#stTipoPercentualInformado_'.$stTipoPercentualInformado.'").attr("checked", true);';
            if ($stTipoPercentualInformado == 'S') {
                $stJs .= getJsPorcentagem('sintetica');
            } else {
                $stJs .= getJsPorcentagem();
            }

        } else {
            $stJs .= 'jq("#stTipoPercentualInformado_A").attr("checked", true);';
            $stJs .= 'jq("#spnPorcentagem").html(\'\');';
            $stJs .= 'jq("#spnListagemReceitas").html(\''.montaListagemReceita().'\');';
            $stJs .= "ajustesListagem();";
        }
        $stJs .= "jq('input[type=\'radio\']:disabled').each(function () { jq(this).removeAttr('disabled'); });";
    } else {
        $stJs .= 'jq("#spnListagemReceitas").html(\'\');';
        $stJs .= 'jq("#stTipoPercentualInformado_A").attr("checked", true);';
        $stJs .= 'jq("#spnPorcentagem").html(\'\');';
        $stJs .= "jq('input[type=\'radio\']').each(function () { jq(this).attr('disabled','disabled'); });";
    }
    break;
}

echo $stJs;

function getRecordSetEstimativaOrcamentaria($inCodPPA)
{
    // Realiza a pesquisa para ver se já existe dados cadastrados para o codigo de ppa informado, caso exista, preeche os dados na tela
    $obTPPAPPAEstimativaOrcamentariaBase = new TPPAPPAEstimativaOrcamentariaBase;
    $stCondicao = "\n WHERE ".$obTPPAPPAEstimativaOrcamentariaBase->getTabela().".cod_ppa = ".$inCodPPA;
    $obTPPAPPAEstimativaOrcamentariaBase->recuperaTodos($rsPPAEstimativa, $stCondicao, $stOrderBy);

    return $rsPPAEstimativa;
}

function getJsPorcentagem($stTipo='')
{
    $stJs .= 'jq("#spnListagemReceitas").html("");';
    $stJs .= 'jq("#spnPorcentagem").html("");';
    $stJs .= 'jq("#spnListagemReceitas").html(\''.montaListagemReceita().'\');';
    $stJs .= "ajustesListagem();";

    if ($stTipo == 'sintetica') {
        $stJs .= "\n jq('#spnPorcentagem').html('".montaFormularioSintetico()."');";
        $stJs .= "\n montaEventoInput();";
        $stJs .= 'bloqueiaPorcegemLista(true);';
    } else {
        $stJs .= 'bloqueiaPorcegemLista(false);';
    }

    $rsEstimativaOrcamentaria = getRecordSetEstimativaOrcamentaria($_GET['inCodPPA']);
    $stJs .= getJsPreecheValoresListagem($rsEstimativaOrcamentaria);

    $arEstimativa = $rsEstimativaOrcamentaria->getElementos();
    if ($arEstimativa[0]['tipo_percentual_informado'] == 'S') {
        $stJs .= "\n jq('#flPorcentagemAno1').val(jq('#flAno1_5_A_5').val());";
        $stJs .= "\n jq('#flPorcentagemAno2').val(jq('#flAno2_5_A_5').val());";
        $stJs .= "\n jq('#flPorcentagemAno3').val(jq('#flAno3_5_A_5').val());";
        $stJs .= "\n jq('#flPorcentagemAno4').val(jq('#flAno4_5_A_5').val());";
    }

    $stJs .= 'calculaSoma();';

    return $stJs;
}

function getJsPreecheValoresListagem($rsPPAEstimativa)
{
    $stJs = '';
    while (!$rsPPAEstimativa->eof()) {
        $inNumero = $rsPPAEstimativa->getCampo('cod_receita');
        if ($rsPPAEstimativa->getCampo('valor') != 0) {
            $stJs .= "\n jq('#flValorReceita_".$inNumero."_A_".$inNumero."').val(retornaFormatoMonetario(".$rsPPAEstimativa->getCampo('valor')."));";
        }
        if ($rsPPAEstimativa->getCampo('percentual_ano_1') != 0) {
            $stJs .= "\n jq('#flAno1_".$inNumero."_A_".$inNumero."').val(retornaFormatoMonetario(".$rsPPAEstimativa->getCampo('percentual_ano_1')."));";
        }
        if ($rsPPAEstimativa->getCampo('percentual_ano_2') != 0) {
            $stJs .= "\n jq('#flAno2_".$inNumero."_A_".$inNumero."').val(retornaFormatoMonetario(".$rsPPAEstimativa->getCampo('percentual_ano_2')."));";
        }
        if ($rsPPAEstimativa->getCampo('percentual_ano_3') != 0) {
            $stJs .= "\n jq('#flAno3_".$inNumero."_A_".$inNumero."').val(retornaFormatoMonetario(".$rsPPAEstimativa->getCampo('percentual_ano_3')."));";
        }
        if ($rsPPAEstimativa->getCampo('percentual_ano_4') != 0) {
            $stJs .= "\n jq('#flAno4_".$inNumero."_A_".$inNumero."').val(retornaFormatoMonetario(".$rsPPAEstimativa->getCampo('percentual_ano_4')."));";
        }

        $rsPPAEstimativa->proximo();
    }

    return $stJs;
}

function montaListagemReceita()
{
    $obTPPAEstimativaOrcamentariaBase = new TPPAEstimativaOrcamentariaBase;
    $obTPPAEstimativaOrcamentariaBase->recuperaTodos($rsEstimativaOrcamentariaBase, '', 'estimativa_orcamentaria_base.cod_receita');

    $arDados = $rsEstimativaOrcamentariaBase->getElementos();
    $arDados[25] = array(
            'cod_receita' => 26
        ,   'cod_estrutural' => '<b>TOTAL DAS RECEITAS</b>'
        ,   'descricao '=> ''
        ,   'tipo' => 'S'
    );

    $rsListagem = new RecordSet;
    $rsListagem->preenche($arDados);

    $obValorReceita = new Numerico;
    $obValorReceita->setName('flValorReceita_[cod_receita]_[tipo]');
    $obValorReceita->setLabel(true);
    $obValorReceita->setClass('valor');
    $obValorReceita->setValue("");
    $obValorReceita->setMaxLength(14);
    $obValorReceita->setSize(15);

    $obAno1 = new Numerico;
    $obAno1->setName('flAno1_[cod_receita]_[tipo]');
    $obAno1->setClass('porcentagem');
    $obAno1->setLabel(true);

    $obAno2 = new Numerico;
    $obAno2->setName('flAno2_[cod_receita]_[tipo]');
    $obAno2->setClass('porcentagem');
    $obAno2->setLabel(true);

    $obAno3 = new Numerico;
    $obAno3->setName('flAno3_[cod_receita]_[tipo]');
    $obAno3->setClass('porcentagem');
    $obAno3->setLabel(true);

    $obAno4 = new Numerico;
    $obAno4->setName('flAno4_[cod_receita]_[tipo]');
    $obAno4->setClass('porcentagem');
    $obAno4->setLabel(true);

    $obTableReceitas = new Table;
    $obTableReceitas->setId('tableReceita');
    $obTableReceitas->setRecordset($rsListagem);

    $obTableReceitas->Head->addCabecalho('Receitas' , 50);
    $obTableReceitas->Head->addCabecalho('Valor'    , 10);
    $obTableReceitas->Head->addCabecalho('Ano 1 (%)', 10);
    $obTableReceitas->Head->addCabecalho('Ano 2 (%)', 10);
    $obTableReceitas->Head->addCabecalho('Ano 3 (%)', 10);
    $obTableReceitas->Head->addCabecalho('Ano 4 (%)', 10);

    $obTableReceitas->Body->addCampo('[cod_estrutural] [descricao]', 'E');
    $obTableReceitas->Body->addCampo($obValorReceita, 'D');
    $obTableReceitas->Body->addCampo($obAno1        , 'D');
    $obTableReceitas->Body->addCampo($obAno2        , 'D');
    $obTableReceitas->Body->addCampo($obAno3        , 'D');
    $obTableReceitas->Body->addCampo($obAno4        , 'D');

    $obTableReceitas->montaHTML(true);

    return $obTableReceitas->getHTML();
}

function montaFormularioSintetico()
{
    $obPorcentagemAno1 = new Numerico;
    $obPorcentagemAno1->setId    ('flPorcentagemAno1');
    $obPorcentagemAno1->setName  ('flPorcentagemAno1');
    $obPorcentagemAno1->setRotulo('Porcentagem Ano 1');
    $obPorcentagemAno1->setTitle ('Informe a porcentagem do Ano 1.');
    $obPorcentagemAno1->setObrigatorio(true);
    $obPorcentagemAno1->setMaxLength(8);

    $obPorcentagemAno2 = new Numerico;
    $obPorcentagemAno2->setId    ('flPorcentagemAno2');
    $obPorcentagemAno2->setName  ('flPorcentagemAno2');
    $obPorcentagemAno2->setRotulo('Porcentagem Ano 2');
    $obPorcentagemAno2->setTitle ('Informe a porcentagem do Ano 2.');
    $obPorcentagemAno2->setObrigatorio(true);
    $obPorcentagemAno2->setMaxLength(8);

    $obPorcentagemAno3 = new Numerico;
    $obPorcentagemAno3->setId    ('flPorcentagemAno3');
    $obPorcentagemAno3->setName  ('flPorcentagemAno3');
    $obPorcentagemAno3->setRotulo('Porcentagem Ano 3');
    $obPorcentagemAno3->setTitle ('Informe a porcentagem do Ano 3.');
    $obPorcentagemAno3->setObrigatorio(true);
    $obPorcentagemAno3->setMaxLength(8);

    $obPorcentagemAno4 = new Numerico;
    $obPorcentagemAno4->setId    ('flPorcentagemAno4');
    $obPorcentagemAno4->setName  ('flPorcentagemAno4');
    $obPorcentagemAno4->setRotulo('Porcentagem Ano 4');
    $obPorcentagemAno4->setTitle ('Informe a porcentagem do Ano 4.');
    $obPorcentagemAno4->setObrigatorio(true);
    $obPorcentagemAno4->setMaxLength(8);

    $obFormulario = new Formulario;
    $obFormulario->addComponente($obPorcentagemAno1);
    $obFormulario->addComponente($obPorcentagemAno2);
    $obFormulario->addComponente($obPorcentagemAno3);
    $obFormulario->addComponente($obPorcentagemAno4);
    $obFormulario->montaInnerHTML();

    return $obFormulario->getHTML();
}

?>
