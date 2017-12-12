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
* Arquivo de implementação de relatórios
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 29194 $
$Name$
$Author: domluc $
$Date: 2008-04-15 10:03:13 -0300 (Ter, 15 Abr 2008) $

Casos de uso: uc-01.06.99
*/

include_once '../../../pacotes/FrameworkHTML.inc.php';
include_once '../../../framework/include/cabecalho.inc.php'; //Insere o início da página html
include_once '../../../framework/legado/funcoesLegado.lib.php';
include_once '../../../framework/legado/botoesPdfLegado.class.php';
include_once '../../../framework/legado/paginacaoLegada.class.php';
include_once '../../../framework/legado/mascarasLegado.lib.php';

    $ctrl  = $_REQUEST['ctrl'];
    $dataInicial = $_REQUEST['dataInicial'];
    $dataFinal = $_REQUEST['dataFinal'];
    $ordem = $_REQUEST['ordem'];
    $pagina = $_REQUEST['pagina'];

    $obFormulario = new FormularioAbas;
    $obFormulario->addForm( $obForm );
    $obFormulario->setAjuda('UC-01.06.99');
    $obFormulario->show();

    if (!(isset($ctrl))) {
        $ctrl = 0;

    }
    $resultInicial = hoje();
    $resultFinal   = hoje();
?>
   <script type="text/javascript">
     function zebra(id, classe)
     {
            var tabela = document.getElementById(id);
            var linhas = tabela.getElementsByTagName("tr");
            for (var i = 0; i < linhas.length; i++) {
                ((i%2) == 0) ? linhas[i].className = classe : void(0);
            }
        }
   </script>
<?php
    switch ($ctrl) {
        case 0:
?>
        <script type="text/javascript">
        //compara a data 1 com a data 2
        //retorna 1 => data 1 maior que data 2
        //        0 => datas iguais
        //       -1 => data 1 menor que data 2
        //        2 => numero errado de digitos
        //hora => coloca a hora na comparacao
        function compareDateTime(valDate1, valDate2, bhora)
        {
            dateMask = "E";
            if (bhora == true) {
                if (valDate1.length < 18 || valDate2.length < 18) {
                    return 2;
                }
            } else {
                if (valDate1.length < 10 || valDate2.length < 10) {
                    return 2;
                }
            }
            var dia = "";
            var mes = "";
            var ano = "";
            var hora = "";
            var minuto1 = "";
            var minuto = "";
            if (bhora == true) {
                hora = valDate1.substring(13, 15);
                minuto = valDate1.substring(15);
                minuto1 = minuto;
            }
            if (dateMask == 'U') { //Americano
                mes = valDate1.substring(0, 2);
                dia = valDate1.substring(3, 5);
                ano = valDate1.substring(6, 10);
            } else if (dateMask == 'E') { //Europeu
                dia = valDate1.substring(0, 2);
                mes = valDate1.substring(3, 5);
                ano = valDate1.substring(6, 10);
           } else { //Geral
                ano = valDate1.substring(0, 4);
                mes = valDate1.substring(5, 7);
                dia = valDate1.substring(8, 10);
            }
            var somaTotal1 = parseInt(ano+mes+dia, 10);
            if (bhora == true) {
                somaTotal1 = parseInt(ano+mes+dia+hora+minuto, 10);
            }
            if (bhora == true) {
                hora = valDate2.substring(13, 15);
                minuto = valDate2.substring(15);
            }
            if (dateMask == 'U') { //Americano
                mes = valDate2.substring(0, 2);
                dia = valDate2.substring(3, 5);
                ano = valDate2.substring(6, 10);
            } else if (dateMask == 'E') { //Europeu
                dia = valDate2.substring(0, 2);
                mes = valDate2.substring(3, 5);
                ano = valDate2.substring(6, 10);
            } else { //Geral
                ano = valDate2.substring(0, 4);
                mes = valDate2.substring(5, 7);
                dia = valDate2.substring(8, 10);
            }
            var somaTotal2 = parseInt(ano+mes+dia, 10);
            if (bhora == true) {
                somaTotal2 = parseInt(ano+mes+dia+hora+minuto, 10);
            }

            somaTotal1 = somaTotal1+minuto1;
            somaTotal2 = somaTotal2+minuto;

            if (somaTotal1 == somaTotal2) {
                return 0;
            } else if (somaTotal1 > somaTotal2) {
                return 1;
            } else {
                return -1;
            }
        }

            function ValidaData()
            {
                var mensagem = "";
                var erro = false;
                var dataI;
                var dataF;

                dataI = document.frm.dataInicial.value;
                dataF = document.frm.dataFinal.value;

            //Não executar a busca sem que haja pelo menos um parâmetro informado
            if ( compareDateTime(dataI, dataF, false) == 1 ) {
                mensagem += "@Data inicial não pode ser maior que a data final.";
                erro = true;
            }

            campoDataInicial = trim( dataI );
            if (campoDataInicial == "") {
                mensagem += "@Informe uma data inicial";
                erro = true;
             }
             campoDataFinal = trim( dataF );
            if (campoDataFinal == "") {
                mensagem += "@Informe uma data final";
                erro = true;
             }

            if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
                return !(erro);
            }

                function Salvar()
                {
                  if (ValidaData()) {
                    document.frm.action = "relatorioVolume.php?<?=Sessao::getId()?>&ctrl=1";
                    document.frm.submit();
                  }
                }

        </script>

            <form name="frm" action="relatorioVolume.php?<?=Sessao::getId()?>" method="post">
                <table width="100%">
                    <tr>
                        <td class=alt_dados colspan=2>
                            Dados do filtro
                        </td>
                    </tr>
                    <tr>
                        <td class=label>
                            *Período
                        </td>
                        <td class=field>
                            <input type="text" name="dataInicial" size=10 value="<?=$resultInicial?>"
                            onkeyup="javascript: mascaraDinamico('99/99/9999', this, event)">
                                &nbsp; a &nbsp;
                            <input type="text" name="dataFinal" size=10 value="<?=$resultFinal?>"
                            onkeyup="javascript: mascaraDinamico('99/99/9999', this, event)">
                        </td>
                    </tr>
                <tr>
                    <td class=label>
                        Ordem
                    </td>
                    <td class=field>
                        <select name=ordem>
                            <option value="1">Código Estrutural</option>
                            <option value="2">Descrição</option>
                        </select>
                    </td>
                </tr>

                    <tr>
                        <td class=field colspan="2">
                            <?=geraBotaoOk();?>
                        </td>
                    </tr>
                </table>
            </form>
<?php
        break;

        case 1:

            ?>
                <script type="text/javascript">
                    function SalvarVolume()
                    {
                        document.frm.action = "relVolume.php?<?=Sessao::getId()?>&ctrl=1&ordem=<?=$ordem?>&dataInicial=<?=$dataInicial?>&dataFinal=<?=$dataFinal?>";
                        document.frm.submit();
                    }
                </script>
                <form name="frm" action="relatorioVolume.php?<?=Sessao::getId()?>" method="post">
            <?php

        $verificador = false;
        $ok = true;
        $vetSetor = explode(".", $codMasSetor);
        $anoSetor = explode("/", $vetSetor[3]);
        $explodedatainicial = explode("/", $dataInicial);
        $explodedatafinal = explode ("/", $dataFinal);
        $dataI = $explodedatainicial[2]."-".$explodedatainicial[1]."-".$explodedatainicial[0];
        $dataF = $explodedatafinal[2]."-".$explodedatafinal[1]."-".$explodedatafinal[0];
        $html = "
            <table width=100% id='processos'>
            <tr>
                <td class='alt_dados' colspan='11'>
                    Registros de processos
                </td>
            </tr>
            <tr>
                <td class='alt_dados' colspan='11'>
                    Período = $dataInicial a $dataFinal
                </td>
            </tr>
            <tr>
                <td class='labelcenterCabecalho' width='5%' >&nbsp;</td>
                <td class='labelcenterCabecalho' style='vertical-align : middle;'>Setores que Tramitam Processos</td>
                <td class='labelcenterCabecalho' style='vertical-align : middle;'>Quantidade Recebida</td>
                <td class='labelcenterCabecalho' style='vertical-align : middle;'>Quantidade Encaminhada</td>
            </tr>
            ";

        $sql  = "
                 SELECT distinct orgao.cod_orgao
                      , orgao_nivel.cod_organograma 
                      , organograma.fn_consulta_orgao(orgao_nivel.cod_organograma, orgao.cod_orgao) as mascara_reduzida
                      , recuperaDescricaoOrgao(orgao.cod_orgao, '".$dataF."'::date) as codunidsetor
                      , COALESCE(processos_recebidos.qtd_recebidos, 0) AS qtd_recebidos 
                      , COALESCE(processos_encaminhados.qtd_encaminhados, 0) AS qtd_encaminhados
                   FROM organograma.orgao
                   
             INNER JOIN organograma.orgao_nivel
                     ON orgao.cod_orgao = orgao_nivel.cod_orgao 
              
              LEFT JOIN (SELECT COUNT(*) AS qtd_recebidos
                              , usuario.cod_orgao          
                           FROM public.sw_processo
                           
                     INNER JOIN public.sw_andamento
                             ON sw_andamento.ano_exercicio = sw_processo.ano_exercicio
                            AND sw_andamento.cod_processo  = sw_processo.cod_processo
                            
                     INNER JOIN public.sw_recebimento
                             ON sw_recebimento.ano_exercicio = sw_andamento.ano_exercicio
                            AND sw_recebimento.cod_processo  = sw_andamento.cod_processo
                            AND sw_recebimento.cod_andamento = sw_andamento.cod_andamento
                            
                     INNER JOIN sw_assinatura_digital
                             ON sw_assinatura_digital.ano_exercicio = sw_andamento.ano_exercicio
                            AND sw_assinatura_digital.cod_processo  = sw_andamento.cod_processo
                            AND sw_assinatura_digital.cod_andamento = sw_andamento.cod_andamento

                     INNER JOIN administracao.usuario
                             ON usuario.numcgm = sw_assinatura_digital.cod_usuario
                          
                          WHERE sw_processo.cod_situacao = 3 -- Valor fixo \n ";
        
        if ($dataInicial != '' and $dataFinal != '') {
            $sql .= "       AND TO_DATE(sw_recebimento.timestamp::text, 'yyyy-mm-dd') BETWEEN '$dataI' AND \n";
            $sql .= "'$dataF'\n";
        } elseif ($dataInicial) {
            $sql .= "       AND TO_DATE(sw_recebimento.timestamp::text, 'yyyy-mm-dd') =  '$dataI' \n";
        }
        
        $sql .= "      GROUP BY usuario.cod_orgao
                        ) AS processos_recebidos
                       ON processos_recebidos.cod_orgao = orgao.cod_orgao
                    
                LEFT JOIN (SELECT COUNT(*) AS qtd_encaminhados
                                , sw_andamento.cod_orgao
                             FROM public.sw_processo
                           
                       INNER JOIN public.sw_andamento
                               ON sw_andamento.ano_exercicio = sw_processo.ano_exercicio
                              AND sw_andamento.cod_processo  = sw_processo.cod_processo
                          
                       INNER JOIN sw_ultimo_andamento
                               ON sw_ultimo_andamento.ano_exercicio = sw_andamento.ano_exercicio
                              AND sw_ultimo_andamento.cod_processo  = sw_andamento.cod_processo
                              AND sw_ultimo_andamento.cod_andamento = sw_andamento.cod_andamento
                       
                            WHERE sw_processo.cod_situacao = 2 -- Valor fixo \n";
                                   
        //$sql .= "                     AND sw_andamento.cod_andamento =       (sw_ultimo_andamento.cod_andamento - 1)\n";
        
        if ($dataInicial != '' and $dataFinal != '') {
            $sql .= "         AND TO_DATE(sw_ultimo_andamento.timestamp::text, 'yyyy-mm-dd') BETWEEN '$dataI' AND \n";
            $sql .= "'$dataF'\n";
        } elseif ($dataInicial) {
            $sql .= "         AND TO_DATE(sw_ultimo_andamento.timestamp::text, 'yyyy-mm-dd') =  '$dataI' \n";
        }
        
        $sql .= "     GROUP BY sw_andamento.cod_orgao
                      ) AS processos_encaminhados        
                      ON processos_encaminhados.cod_orgao = orgao.cod_orgao    \n";

        $st_ordenacao = array(
             1 => "mascara_reduzida",
             2 => "codunidsetor");

        Sessao::write('sSQLs',$sql);
        Sessao::write('arOrdem',$st_ordenacao);
        Sessao::write('ordem',$ordem);

        $paginacao = new paginacaoLegada;
        $paginacao->pegaDados(Sessao::read('sSQLs'),"10");
        $paginacao->pegaPagina($pagina);
        $paginacao->complemento = "&ctrl=1&ordem=$ordem";
        $paginacao->complemento .= "&dataInicial=".urlencode($dataInicial)."&dataFinal=".urlencode($dataFinal);
        $paginacao->geraLinks();
        $paginacao->pegaOrder("cod_organograma, ".$st_ordenacao[$ordem],"ASC");
        
        $sSQL = $paginacao->geraSQL();
        
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $stSubTituloPeriodo = "";
        
        if ($dataInicial != '' and $dataFinal != '') {
            $periodo = "stperiodo="."Período: ".$dataInicial." a ".$dataFinal;
            $stSubTituloPeriodo = "Período: ".$dataInicial." a ".$dataFinal;
        } elseif ($dataInicial != '') {
            $periodo = "stperiodo="."Período: ".$dataInicial;
            $stSubTituloPeriodo = "Período: ".$dataInicial;
        }

        print '
        <table id=paginacao width="100">
            <tr>
                <td class="labelcenterTable" title="Salvar Relatório">
                <a href="javascript:SalvarVolume();"><img src="'.CAM_FW_IMAGENS.'botao_salvar.png" border=0></a>
            </tr>
        </table>
            ';

        $dbEmp->fechaBD();
        $dbEmp->vaiPrimeiro();
        if($dbEmp->numeroDeLinhas==0)
            $ok = false;
        if ($ok) {
            $count = $paginacao->contador();
            while (!$dbEmp->eof()) {
                $codOrgao           = $dbEmp->pegaCampo("cod_orgao");
                $codunidsetor       = $dbEmp->pegaCampo("codunidsetor");
                $qtd_recebidos      = $dbEmp->pegaCampo("qtd_recebidos");
                $qtd_encaminhados   = $dbEmp->pegaCampo("qtd_encaminhados");
                $mascara_reduzida   = $dbEmp->pegaCampo("mascara_reduzida");
                $dbEmp->vaiProximo();

                $html .= "<tr>";
                $html .= "<td class=labelcenterTable>".$count++."</td>\n";
                $html .= "<td class=show_dados>".$mascara_reduzida." - ".$codunidsetor."</td>\n";
                $html .= "<td class=show_dados>".$qtd_recebidos."</td>\n";
                $html .= "<td class=show_dados>".$qtd_encaminhados."</td>\n";
                $html .= "</tr>";
            }
        }
        $dbEmp->limpaSelecao();
        $html .= "</table>";

        if(!$ok)
            echo "<br><b><span class='itemText'>Nenhum registro encontrado!</span></b><br><br>";
        else
            echo $html;
            echo "<table id= 'paginacao' width=695 align=center><tr><td align=center><font size=2>";
                $paginacao->mostraLinks();
            echo "</font></tr></td></table>";

            break;

            case 100:
                include(CAM_FW_LEGADO."filtrosCASELegado.inc.php");
            break;

    }
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
<script>zebra('processos','zb');</script>
