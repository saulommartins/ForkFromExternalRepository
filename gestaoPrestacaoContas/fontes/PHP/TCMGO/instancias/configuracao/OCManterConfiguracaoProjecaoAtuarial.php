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
    * @author Analista: Carlos Adriano
    * @author Desenvolvedor: Carlos Adriano
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once(TTGO."TTCMGOProjecaoAtuarial.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoProjecaoAtuarial";
$pgFilt = "FL".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stCtrl = $_REQUEST['stCtrl'];

switch ($stCtrl) {
    case 'buscaLista':
        $rsProjecaoAtuarial = new RecordSet();

        $obTProjecaoAtuarial = new TTCMGOProjecaoAtuarial();
        $obTProjecaoAtuarial->setDado('num_orgao', $_REQUEST['inOrgao']);
        $obTProjecaoAtuarial->recuperaPorOrgao( $rsProjecaoAtuarial );

        $obTxtExercicio = new Inteiro;
        $obTxtExercicio->setName     ('exercicio[]');
        $obTxtExercicio->setId       ('exercicio');
        $obTxtExercicio->setValue    ('');
        $obTxtExercicio->setRotulo   ('Exercicio');
        $obTxtExercicio->setSize     (25);
        $obTxtExercicio->setMaxLength(4);
        $obTxtExercicio->setReadOnly(true);
        $obTxtExercicio->montaHTML();

        $obTxtVlReceitaPrevidenciaria = new Moeda;
        $obTxtVlReceitaPrevidenciaria->setName     ('vlReceitaPrevidenciaria[]');
        $obTxtVlReceitaPrevidenciaria->setId       ('vlReceitaPrevidenciaria');
        $obTxtVlReceitaPrevidenciaria->setValue    ('');
        $obTxtVlReceitaPrevidenciaria->setRotulo   ('Valor projetado das receitas previdenciárias');
        $obTxtVlReceitaPrevidenciaria->setSize     (25);
        $obTxtVlReceitaPrevidenciaria->setMaxLength(13);
        $obTxtVlReceitaPrevidenciaria->montaHTML();

        $obTxtVlDespesaPrevidenciaria = new Moeda;
        $obTxtVlDespesaPrevidenciaria->setName     ('vlDespesaPrevidenciaria[]');
        $obTxtVlDespesaPrevidenciaria->setId       ('vlDespesaPrevidenciaria');
        $obTxtVlDespesaPrevidenciaria->setValue    ('');
        $obTxtVlDespesaPrevidenciaria->setRotulo   ('Valor projetado das despesas previdenciárias');
        $obTxtVlDespesaPrevidenciaria->setSize     (25);
        $obTxtVlDespesaPrevidenciaria->setMaxLength(13);
        $obTxtVlDespesaPrevidenciaria->montaHTML();

        $obTxtVlSaldoFinanceiroExercicio = new Moeda;
        $obTxtVlSaldoFinanceiroExercicio->setName     ('vlSaldoFinanceiroExercicio[]');
        $obTxtVlSaldoFinanceiroExercicio->setId       ('vlSaldoFinanceiroExercicio');
        $obTxtVlSaldoFinanceiroExercicio->setValue    ('');
        $obTxtVlSaldoFinanceiroExercicio->setRotulo   ('Valor do saldo financeiro do exercício anterior');
        $obTxtVlSaldoFinanceiroExercicio->setSize     (25);
        $obTxtVlSaldoFinanceiroExercicio->setMaxLength(13);

        unset($arLista);
        $exercicio = Sessao::getExercicio() - 1;
        for ($i=0; $i<75; $i++) {
            $obTxtExercicio->setValue($exercicio+$i);

            foreach ($rsProjecaoAtuarial->arElementos as $projecao) {
                if ($projecao['exercicio'] == ($exercicio+$i)) {
                    $vlReceita = number_format($projecao['vl_receita'], '2', ',', '.');
                    $vlDespesa = number_format($projecao['vl_despesa'], '2', ',', '.');
                    $vlSaldo   = number_format($projecao['vl_saldo']  , '2', ',', '.');

                    $obTxtVlReceitaPrevidenciaria->setValue($vlReceita != '0,00' ? $vlReceita : '');
                    $obTxtVlDespesaPrevidenciaria->setValue($vlDespesa != '0,00' ? $vlDespesa : '');
                    $obTxtVlSaldoFinanceiroExercicio->setValue($vlSaldo != '0,00' ? $vlSaldo : '');
                }
            }

            $obTxtExercicio->montaHTML();
            $obTxtVlReceitaPrevidenciaria->montaHTML();
            $obTxtVlDespesaPrevidenciaria->montaHTML();
            $obTxtVlSaldoFinanceiroExercicio->montaHTML();

            $arLista[$i]['exercicio'] = $obTxtExercicio->getHtml();
            $arLista[$i]['receita']   = $obTxtVlReceitaPrevidenciaria->getHtml();
            $arLista[$i]['despesa']   = $obTxtVlDespesaPrevidenciaria->getHtml();
            $arLista[$i]['saldo']     = $obTxtVlSaldoFinanceiroExercicio->getHtml();
        }

        $rsLista = new RecordSet();
        $rsLista->preenche ( $arLista );

        $obLista = new Lista();
        $obLista->setRecordSet( $rsLista );
        $obLista->setNumeracao( false );
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo ( "Detalhamento da projeção atuarial do RPPS" );

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Exercício" );
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Valor projetado das receita" );
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Valor projetado das despesa" );
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Saldo financeiro exercício anterior" );
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo('exercicio');
        $obLista->ultimoDado->setAlinhamento( 'CENTER' );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo('receita');
        $obLista->ultimoDado->setAlinhamento( 'CENTER' );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo('despesa');
        $obLista->ultimoDado->setAlinhamento( 'CENTER' );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo('saldo');
        $obLista->ultimoDado->setAlinhamento( 'CENTER' );
        $obLista->commitDado();

        //****************************************//
        // Monta formulário
        //****************************************//
        $obFormulario = new Formulario;
        $obFormulario->addForm  ('');

        $obFormulario->addLista( $obLista );

        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();

        $js = "<script>window.parent.frames['telaPrincipal'].document.getElementById('spnLista').innerHTML = '".$stHtml."';</script>";
        echo $js;
    break;
}
