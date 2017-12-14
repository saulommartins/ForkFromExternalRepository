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
 * @author Analista: Dagiane Vieira
 * @author Desenvolvedor: Michel Teixeira
 *
 * $Id: OCManterConfiguracaoProjecaoAtuarial.php 61820 2015-03-06 16:15:57Z michel $
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGProjecaoAtuarial.class.php';

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
        $stHtml = '&nbsp;';
        $stExercicios = '';

        if(isset($_REQUEST['inCodEntidadeRPPS'])&&$_REQUEST['inCodEntidadeRPPS']!=''){
            $rsProjecaoAtuarial = new RecordSet();

            $obTProjecaoAtuarial = new TTCEMGProjecaoAtuarial();
            $obTProjecaoAtuarial->setDado('cod_entidade'        , $_REQUEST['inCodEntidadeRPPS']  );
            $obTProjecaoAtuarial->setDado('exercicio_entidade'  , Sessao::getExercicio());
            $obTProjecaoAtuarial->recuperaPorEntidade( $rsProjecaoAtuarial );

            unset($arLista);
            $inExercicioInicial = Sessao::getExercicio() - 1;
            for ($i=0; $i<75; $i++) {
                $inExercicioUltimo = ($inExercicioInicial+$i);
                $idExercicio = '_'.$inExercicioUltimo;

                $obTxtExercicio = new Label;
                $obTxtExercicio->setRotulo  ( 'Exercicio'               );
                $obTxtExercicio->setName    ( 'exercicio'.$idExercicio  );
                $obTxtExercicio->setId      ( 'exercicio'.$idExercicio  );
                $obTxtExercicio->setValue   ( $inExercicioUltimo        );

                $obTxtVlPatronal = new Moeda;
                $obTxtVlPatronal->setName       ('vlPatronal'.$idExercicio  );
                $obTxtVlPatronal->setId         ('vlPatronal'.$idExercicio  );
                $obTxtVlPatronal->setRotulo     ('Contribuição Patronal'    );
                $obTxtVlPatronal->setSize       (25);
                $obTxtVlPatronal->setMaxLength  (13);

                $obTxtVlReceitaPrevidenciaria = new Moeda;
                $obTxtVlReceitaPrevidenciaria->setName      ('vlReceitaPrevidenciaria'.$idExercicio         );
                $obTxtVlReceitaPrevidenciaria->setId        ('vlReceitaPrevidenciaria'.$idExercicio         );
                $obTxtVlReceitaPrevidenciaria->setRotulo    ('Receita' );
                $obTxtVlReceitaPrevidenciaria->setSize      (25);
                $obTxtVlReceitaPrevidenciaria->setMaxLength (13);

                $obTxtVlDespesaPrevidenciaria = new Moeda;
                $obTxtVlDespesaPrevidenciaria->setName      ('vlDespesaPrevidenciaria'.$idExercicio         );
                $obTxtVlDespesaPrevidenciaria->setId        ('vlDespesaPrevidenciaria'.$idExercicio         );
                $obTxtVlDespesaPrevidenciaria->setRotulo    ('Despesa' );
                $obTxtVlDespesaPrevidenciaria->setSize      (25);
                $obTxtVlDespesaPrevidenciaria->setMaxLength (13);

                $obTxtVlRPPS = new Moeda;
                $obTxtVlRPPS->setName       ('vlRPPS'.$idExercicio  );
                $obTxtVlRPPS->setId         ('vlRPPS'.$idExercicio  );
                $obTxtVlRPPS->setRotulo     ('Repasse do RPPS'      );
                $obTxtVlRPPS->setSize       (25);
                $obTxtVlRPPS->setMaxLength  (13);

                foreach ($rsProjecaoAtuarial->arElementos as $projecao) {
                    if ($projecao['exercicio'] == $inExercicioUltimo) {
                        $vlPatronal = number_format($projecao['vl_patronal'], '2', ',', '.');
                        $vlReceita  = number_format($projecao['vl_receita'] , '2', ',', '.');
                        $vlDespesa  = number_format($projecao['vl_despesa'] , '2', ',', '.');
                        $vlRPPS     = number_format($projecao['vl_rpps']    , '2', ',', '.');

                        $obTxtVlPatronal->setValue              ($vlPatronal != '0,00'  ? $vlPatronal   : '');
                        $obTxtVlReceitaPrevidenciaria->setValue ($vlReceita != '0,00'   ? $vlReceita    : '');
                        $obTxtVlDespesaPrevidenciaria->setValue ($vlDespesa != '0,00'   ? $vlDespesa    : '');
                        $obTxtVlRPPS->setValue                  ($vlRPPS    != '0,00'   ? $vlRPPS       : '');
                    }
                }

                $obTxtExercicio->montaHTML();
                $obTxtVlPatronal->montaHTML();
                $obTxtVlReceitaPrevidenciaria->montaHTML();
                $obTxtVlDespesaPrevidenciaria->montaHTML();
                $obTxtVlRPPS->montaHTML();

                $arLista[$i]['exercicio']   = $obTxtExercicio->getHtml();
                $arLista[$i]['patronal']    = $obTxtVlPatronal->getHtml();
                $arLista[$i]['receita']     = $obTxtVlReceitaPrevidenciaria->getHtml();
                $arLista[$i]['despesa']     = $obTxtVlDespesaPrevidenciaria->getHtml();
                $arLista[$i]['rpps']        = $obTxtVlRPPS->getHtml();
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
            $obLista->ultimoCabecalho->addConteudo( "Contribuição Patronal" );
            $obLista->ultimoCabecalho->setWidth( 5 );
            $obLista->commitCabecalho();

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo( "Receita" );
            $obLista->ultimoCabecalho->setWidth( 5 );
            $obLista->commitCabecalho();

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo( "Despesa" );
            $obLista->ultimoCabecalho->setWidth( 5 );
            $obLista->commitCabecalho();

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo( "Repasse do RPPS" );
            $obLista->ultimoCabecalho->setWidth( 5 );
            $obLista->commitCabecalho();

            $obLista->addDado();
            $obLista->ultimoDado->setCampo('exercicio');
            $obLista->ultimoDado->setAlinhamento( 'CENTER' );
            $obLista->commitDado();

            $obLista->addDado();
            $obLista->ultimoDado->setCampo('patronal');
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
            $obLista->ultimoDado->setCampo('rpps');
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

            $stExercicios = $inExercicioInicial."_".$inExercicioUltimo;
        }

        $js = "<script>window.parent.frames['telaPrincipal'].document.getElementById('spnLista').innerHTML = '".$stHtml."';";
        $js .= "window.parent.frames['telaPrincipal'].document.getElementById('stExercicios').value = '".$stExercicios."';</script>";

        echo $js;
    break;
}
