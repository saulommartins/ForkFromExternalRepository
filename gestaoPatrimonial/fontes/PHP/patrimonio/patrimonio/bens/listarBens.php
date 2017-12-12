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
    * Arquivo que lista os Bens
    * Data de Criação   : ?????

    * @author Desenvolvedor ?????

    * @ignore

    $Revision: 25372 $
    $Name$
    $Autor:$
    $Date: 2007-09-11 15:38:44 -0300 (Ter, 11 Set 2007) $

    * Casos de uso: uc-03.01.06
*/

/*
$Log$
Revision 1.60  2007/09/11 18:38:44  bruce
Ticket#10082#

Revision 1.59  2007/09/10 13:04:10  bruce
Ticket#10064#

Revision 1.58  2007/08/23 12:45:37  bruce
Bug#9972#

Revision 1.57  2007/06/13 13:49:29  bruce
Bug #8349#

Revision 1.56  2007/06/04 13:26:41  rodrigo
Bug #8987#

Revision 1.55  2007/04/25 22:11:08  rodrigo_sr
Bug #8335#

Revision 1.54  2007/04/25 14:33:24  rodrigo_sr
Bug #8348#

Revision 1.53  2006/11/27 15:54:05  larocca
Bug #6927#

Revision 1.52  2006/11/06 15:55:25  hboaventura
bug #6880#

Revision 1.51  2006/07/27 12:21:07  fernando
Bug #6426#

Revision 1.50  2006/07/27 12:15:04  fernando
Bug #6426#

Revision 1.49  2006/07/13 19:38:30  fernando
correção de aspas simples e duplas

Revision 1.48  2006/07/12 19:27:20  gelson
Correção dos hints

Revision 1.47  2006/07/06 17:04:09  gelson
Modificada a mensagem do hint da ordenação.

Revision 1.46  2006/07/06 14:06:36  diego
Retirada tag de log com erro.

Revision 1.45  2006/07/06 12:11:27  diego

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/dataBaseLegado.class.php';

?>

<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/ifuncoesJs.js" type="text/javascript"></script>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/funcoesJs.js" type="text/javascript"></script>

<?php

    switch ($ctrl_frm) {

/****************************************/
// preenche Natureza e Grupo
/****************************************/
        case 1:

            include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/filtrosNGE.inc.php';
            exit();

        break;

/****************************************/
// lista bens encontrados
/****************************************/
        case 2:

            if ( count( $_POST )> 0 ) {
             $sessao->filtro = $_POST;
            }
            // monta filtros para a consulta
            $filtro_codBem    = "";
            $filtro_NatGrpEsp = "";
            $filtro_numPlaca  = "";
            $filtro_desc      = "";

            // filtro por natureza
            if ($sessao->filtro['codNatureza'] and $sessao->filtro['codNatureza'] != 'xxx') {
                $filtro_NatGrpEsp .= " AND B.cod_natureza = ".$sessao->filtro['codNatureza'];
            }

            // filtro por grupo
            if ($sessao->filtro['codGrupo'] != 'xxx' and $sessao->filtro['codGrupo']) {
                $filtro_NatGrpEsp .= " AND B.cod_grupo = ".$sessao->filtro['codGrupo'];
            }

            // filtro por especie
            if ($sessao->filtro['codEspecie'] != 'xxx' and $sessao->filtro['codEspecie']) {
                $filtro_NatGrpEsp .= " AND B.cod_especie = ".$sessao->filtro['codEspecie'];
            }

            // fltro por codBem
            if (isset($sessao->filtro['codBem']) and $sessao->filtro['codBem']>0) {
                $filtro_codBem = " AND B.cod_bem = ".$sessao->filtro['codBem'];
            }

            // fltro por numPlaca
            if ($sessao->filtro['numPlaca'] != "") {
                switch ($stTipoBuscaDescricao) {
                    case 'inicio':
                        $filtro_numPlaca = " AND lower(ltrim(B.num_placa,0)) like lower('".ltrim($numPlaca,0)."')||'%' ";
                    break;
                    case 'final':
                        $filtro_numPlaca = " AND lower(ltrim(B.num_placa,0)) like '%'||lower('".ltrim($numPlaca,0)."') ";
                    break;
                    case 'contem':
                        $filtro_numPlaca = " AND lower(ltrim(B.num_placa,0)) like '%'||lower('".ltrim($numPlaca,0)."')||'%' ";
                    break;
                    case 'exata':
                        $filtro_numPlaca = " AND lower(ltrim(B.num_placa,0)) = lower('".ltrim($numPlaca,0)."') ";
                    break;
                }
            }

           if ($sessao->filtro['placaIdentificacao'] == 'N') {
               $filtro_numPlaca = "and (B.num_placa is  null or B.num_placa = trim(''))";
            }

            if ($sessao->filtro['placaIdentificacao'] == 'S') {
               if ($sessao->filtro['numPlaca'] != "") {
                    switch ($stTipoBuscaDescricao) {
                        case 'inicio':
                            $filtro_numPlaca = " AND lower(ltrim(B.num_placa,0)) like lower('".ltrim($numPlaca,0)."')||'%' ";
                        break;
                        case 'final':
                            $filtro_numPlaca = " AND lower(ltrim(B.num_placa,0)) like '%'||lower('".ltrim($numPlaca,0)."') ";
                        break;
                        case 'contem':
                            $filtro_numPlaca = " AND lower(ltrim(B.num_placa,0)) like '%'||lower('".ltrim($numPlaca,0)."')||'%' ";
                        break;
                        case 'exata':
                            $filtro_numPlaca = " AND lower(ltrim(B.num_placa,0)) = lower('".ltrim($numPlaca,0)."') ";
                        break;
                    }
               } else {
                    $filtro_numPlaca = " and (B.num_placa is not null or B.num_placa <>trim(''))";
               }
            }
          if ($sessao->filtro['descricao'] > "") {
              $filtro_desc = " AND B.descricao ilike '%".$sessao->filtro['descricao']."%'";
          }

            switch ($sessao->filtro['order']) {
                case 'cod': $ord = "B.cod_bem"; break;
                case 'desc': $ord = "B.descricao"; break;
            }

            if ($sessao->acao == 105) {
               $sSQLs = "
               SELECT
                      B.cod_bem
                    , B.descricao
                    , B.cod_especie
                    , B.cod_grupo
                    , B.cod_natureza
                    , B.num_placa
                    , N.nom_natureza
                    , G.nom_grupo
                    , E.nom_especie
                 FROM
                      patrimonio.bem AS B
                      INNER JOIN patrimonio.natureza AS N
                              ON N.cod_natureza           = B.cod_natureza
                      INNER JOIN patrimonio.grupo AS G
                              ON G.cod_natureza           = B.cod_natureza
                             AND G.cod_grupo              = B.cod_grupo
                      INNER JOIN patrimonio.especie AS E
                              ON E.cod_natureza           = B.cod_natureza
                             AND E.cod_grupo              = B.cod_grupo
                             AND E.cod_especie            = B.cod_especie
                      INNER JOIN patrimonio.manutencao as M
                              ON M.cod_bem                = B.cod_bem
                WHERE
                      B.cod_bem > 0
                  AND M.dt_realizacao IS NULL
                " . $filtro_codBem . $filtro_NatGrpEsp . $filtro_numPlaca . $filtro_desc . " ";
             } elseif ($sessao->acao == 71) {
               $sSQLs = "
                SELECT distinct
                    B.cod_bem,
                    B.descricao,
                    B.cod_especie,
                    B.cod_grupo,
                    B.cod_natureza,
                    B.num_placa,
                    N.nom_natureza,
                    G.nom_grupo,
                    E.nom_especie
                FROM
                    patrimonio.bem          as B,
                    patrimonio.natureza     as N,
                    patrimonio.grupo        as G,
                    patrimonio.especie      as E,
                    patrimonio.manutencao   as M,
                    patrimonio.manutencao_paga as MP
                WHERE
                    B.cod_bem > 0 " . $filtro_codBem . $filtro_NatGrpEsp . $filtro_numPlaca . $filtro_desc . "
                    AND M.cod_bem = B.cod_bem
                    AND N.cod_natureza = B.cod_natureza
                    AND G.cod_natureza = B.cod_natureza
                    AND G.cod_grupo = B.cod_grupo
                    AND E.cod_natureza = B.cod_Natureza
                    AND E.cod_grupo = B.cod_grupo
                    AND E.cod_especie = B.cod_especie
                    AND MP.cod_bem = M.cod_bem
                    AND MP.dt_agendamento = M.dt_agendamento";
            } elseif ($sessao->acao == 69) {
                  $sSQLs = "
                          select    B.cod_bem,
                                    B.descricao,
                                    B.cod_especie,
                                    B.cod_grupo,
                                    B.cod_natureza,
                                    B.num_placa,
                                    N.nom_natureza,
                                    G.nom_grupo,
                                    E.nom_especie,
                                    M.cod_bem||'|'||M.dt_agendamento as chave
                          from       patrimonio.bem as B
                          inner join patrimonio.especie as E
                                   on   (E.cod_especie   = B.cod_especie
                                     and E.cod_grupo     = B.cod_grupo
                                     and E.cod_natureza  = B.cod_natureza)
                          inner join patrimonio.grupo as G
                                   on ( G.cod_grupo = E.cod_grupo AND
                                        G.cod_natureza = E.cod_natureza)
                          inner join patrimonio.natureza as N
                                   on (N.cod_natureza = g.cod_natureza)
                          inner join patrimonio.manutencao as M
                                   on (M.cod_bem = B.cod_bem)
                          where not ( (M.cod_bem||'|'||M.dt_agendamento)
                                      in ( SELECT mp.cod_bem||'|'||mp.dt_agendamento FROM patrimonio.manutencao_paga as mp) )".

                          $filtro_codBem . $filtro_NatGrpEsp . $filtro_numPlaca . $filtro_desc  ;

             } else {
              $sSQLs = "
                SELECT
                    B.cod_bem,
                    B.descricao,
                    B.cod_especie,
                    B.cod_grupo,
                    B.cod_natureza,
                    B.num_placa,
                    N.nom_natureza,
                    G.nom_grupo,
                    E.nom_especie,
                    extract(year from B.dt_aquisicao) as exercicio
                FROM
                    patrimonio.bem          as B,
                    patrimonio.natureza     as N,
                    patrimonio.grupo        as G,
                    patrimonio.especie      as E

                WHERE
                    B.cod_bem > 0 " . $filtro_codBem . $filtro_NatGrpEsp . $filtro_numPlaca. $filtro_desc ."
                    AND N.cod_natureza = B.cod_natureza
                    AND G.cod_natureza = B.cod_natureza
                    AND G.cod_grupo = B.cod_grupo
                    AND E.cod_natureza = B.cod_Natureza
                    AND E.cod_grupo = B.cod_grupo
                    AND E.cod_especie = B.cod_especie";
             }
            if (!isset($pagina)) {
                $sessao->transf = $sSQLs;
                $sessao->transf2 = $ord;
            }

            $complemento = "&ctrl_frm=2";

            if ($sessao->acao == '102') {
                $complemento .= "&controle=1";
            }

            $sSQL =" SELECT configuracao.valor                                            \n";
            $sSQL.="   FROM administracao.configuracao                                    \n";
            $sSQL.="  WHERE configuracao.exercicio  = '".Sessao::getExercicio()."'            \n";
            $sSQL.="    AND configuracao.cod_modulo = 6                                   \n";
            $sSQL.="    AND configuracao.parametro  = 'alterar_bens_exercicio_anterior';  \n";

            $config = new dataBaseLegado;
            $config->abreBD();
            $config->abreSelecao($sSQL);
            $config->vaiPrimeiro();

            $boAlteraExercicioAnt = (trim($config->pegaCampo("valor")=="true"));

            $config->limpaSelecao();
            $config->fechaBD();

            include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/paginacaoLegada.class.php';
            $paginacao = new paginacaoLegada;
            $paginacao->pegaDados($sessao->transf,"10");
            $paginacao->pegaPagina($pagina);
            $paginacao->complemento=$complemento;
            $paginacao->geraLinks();
            //$paginacao->pegaOrder("cod_bem","ASC");
            $paginacao->pegaOrder($sessao->transf2,"ASC");
            $sSQLs = $paginacao->geraSQL();
            $conn = new dataBaseLegado;
            $conn->abreBD();

            $conn->abreSelecao($sSQLs);
            if ( $pagina > 0 and $conn->eof() ) {
                $pagina--;
                $paginacao->pegaPagina($pagina);
                //$paginacao->complemento="&ctrl=1";
                $paginacao->geraLinks();
                $paginacao->pegaOrder("cod_bem","ASC");
                $sSQL = $paginacao->geraSQL();
                $conn->abreSelecao($sSQLs);
            }

            $conn->vaiPrimeiro();

?>
            <table width="100%">
            <tr>
<?php
           if ($sessao->acao == '105') {
?>
                <td class="alt_dados" colspan="8">Bens com Agendamento</td>
<?php
            } else {
?>
                 <td class="alt_dados" colspan="8">Registros de Bens</td>

<?php
          }
?>
            </tr>
            <tr>
                <td class="labelcenter" width="5%">&nbsp;</td>
                <td class="labelcenter" width="10%">Classificação</td>
                <td class="labelcenter" width="10%">Código</td>
                <td class="labelcenter" width="10%">Número da Placa</td>
                <td class="labelcenter" width="70%">Descrição</td>
                <td class="labelcenter" width="5%">&nbsp;</td>
            </tr>
<?php
            $cont = $paginacao->contador();
            if (!$conn->eof()) {
             while (!$conn->eof()) {
                if ($sessao->acao == '69') {

                    if ($codBemAnterior ==  trim($conn->pegaCampo("cod_bem"))) {
                        $registroRepetido = 'sim';
                    } else {
                        $registroRepetido = 'não';
                    }
                    $codBemAnterior = trim($conn->pegaCampo("cod_bem"));
                }
                if ($registroRepetido == 'sim') {
                    $conn->vaiProximo();
                } else {
                    $codGrupof        = trim($conn->pegaCampo("cod_grupo")   );
                    $codNaturezaf     = trim($conn->pegaCampo("cod_natureza"));
                    $codEspecief      = trim($conn->pegaCampo("cod_especie") );
                    $codBemf          = trim($conn->pegaCampo("cod_bem")     );
                    $descricao        = trim($conn->pegaCampo("descricao")   );
                    $descricao        = str_replace('\"', '"', str_replace(chr(13).chr(10)," ",$descricao));
                    $descricao        = str_replace('\\\'', '\'', str_replace(chr(13).chr(10)," ",$descricao));
                    $numPlaca         = trim($conn->pegaCampo("num_placa")   );
                    $nomNatureza      = trim($conn->pegaCampo("nom_natureza"));
                    $nomGrupo         = trim($conn->pegaCampo("nom_grupo"));
                    $nomEspecie       = trim($conn->pegaCampo("nom_especie"));
                    $inExercicio      = trim($conn->pegaCampo("exercicio"));

                    $classificacao = $codNaturezaf.".".$codGrupof.".".$codEspecief;

                $conn->vaiProximo();
?>
                <tr>
                    <td class="labelcenter" width="5%"><?=$cont++;?></td>
                    <td class="show_dados_right">&nbsp;<?=$classificacao;?></td>
                    <td class="show_dados_right">&nbsp;<?=$codBemf;?></td>
                    <td class="show_dados_right">&nbsp;<?=$numPlaca;?></td>
                    <td class="show_dados">&nbsp;<?=$descricao;?></td>

                    <?php
                    if ($sessao->acao == '69' || $sessao->acao == '104')
                        $title = 'Incluir';
                    if ($sessao->acao == '71' || $sessao->acao == '97' )
                        $title = 'Excluir';
                    if ($sessao->acao == '95' || $sessao->acao == '96' || $sessao->acao == '105' )
                        $title = 'Alterar';
                    if ($sessao->acao == '102')
                        $title = 'Transferir';

                    ?>
                    <td class="botao" title="<?=$title;?>">
<?php
                    // monta link e imagem do botao de acordo com a Acao selecionada

                    // Acao => INCLUIR MANUTENCAO DE BEM
                    if ($sessao->acao == '69') {
                        $numPlaca = urlencode($numPlaca);
?>
                        <a href='incluiManutencao.php?<?=Sessao::getId();?>&codbem=<?=$codBemf;?>&numPlaca=<?=$numPlaca?>&ctrl=1&pagina=<?=$pagina?>'>
                        <img src='../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/btneditar.gif' title="" border='0'>
                        </a>
<?php

                    }

                    // Acao => EXCLUIR MANUTENCAO DE BEM
                    if ($sessao->acao == '71') {
?>
                        <a href='excluiManutencao.php?<?=Sessao::getId();?>&codkey=<?=$codBemf;?>&ctrl=1&pagina=<?=$pagina?>'>
                        <img src='../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/btneditar.gif' title="" border='0'>
                        </a>
<?php
                    }

                    // Acao => ALTERAR SITUACAO
                    if ($sessao->acao == '95') {
?>
                        <a href='alteraSituacao.php?<?=Sessao::getId();?>&codBem=<?=$codBemf;?>&controle=1&pagina=<?=$pagina?>'>
                        <img src='../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/btneditar.gif' title="" border='0'>
                        </a>
<?php
                    }

                    // Acao => ALTERAR
                    if ($sessao->acao == '96') {

                        if ( ( !$boAlteraExercicioAnt ) and ( $inExercicio != Sessao::getExercicio() ) ) {
                            $stJs = "alertaAviso('Permissão negada para excluir/alterar bem de exercício anterior. Verificar Configuração.','form','erro','".Sessao::getId()."');\n";
?>
                               <a href='#' onClick= "<?=$stJs?>" >
                               <img src='../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/btneditar.gif' title="" border='0'>
                               </a>
<?php

                        } else {
?>
                              <a href='alteraBem.php?<?=Sessao::getId();?>&codBem=<?=$codBemf;?>&controle=1&pagina=<?=$pagina?>'>
                              <img src='../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/btneditar.gif' title=""  border='0'>
                              </a>
<?php
                        }

                    }

                    // Acao => EXCLUIR
                    if ($sessao->acao == '97') {
                        $descricao = urlencode($descricao);

                        if ( ( !$boAlteraExercicioAnt ) and ( $inExercicio != Sessao::getExercicio() ) ) {
                            $stJs = "alertaAviso('Permissão negada para excluir/alterar bem de exercício anterior. Verificar Configuração.','form','erro','".Sessao::getId()."');\n";
?>
                        <a href='#' onClick= "<?=$stJs?>" >
                        <img src='../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/btnexcluir.gif' title="" border='0'>
                        </a>
<?php

                        } else {
?>
                        <a href='#' onClick="alertaQuestao('../../../../../../gestaoPatrimonial/fontes/PHP/patrimonio/patrimonio/bens/excluiBem.php?<?=Sessao::getId();?>','codBemEx','<?=$codBemf.'-'.$descricao;?>%26pagina=<?=$pagina?>','Bem: <?=$codBemf;?> - <?=$descricao;?>','sn_excluir','<?=Sessao::getId();?>')">
                        <img src='../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/btnexcluir.gif' title="" border='0'>
                        </a>
<?php

                        }

                    }

                    // Acao => TRANSFERIR BENS
                    if ($sessao->acao == '102') {
?>
                        <a href='transfereBens.php?<?=Sessao::getId();?>&codBem=<?=$codBemf;?>&controle=2&pagina=<?=$pagina?>'>
                        <img src='../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/botao_encaminhar.png' title="" border='0'>
                        </a>
<?php
                    }

                    // Acao => AGENDAR MANUTENCAO
                    if ($sessao->acao == '104') {
                        $descricao = urlencode($descricao);
                        $numPlaca = urlencode($numPlaca);
?>
                        <a href='agendaManutencao.php?<?=Sessao::getId();?>&codbem=<?=$codBemf;?>&empenho=<?=$empenho;?>&empenhoExercicio=<?=$empenhoExercicio;?>&numPlaca=<?=$numPlaca;?>&descricao=<?=$descricao;?>&nomNatureza=<?=$nomNatureza?>&nomGrupo=<?=$nomGrupo?>&nomEspecie=<?=$nomEspecie?>&ctrl=1&pagina=<?=$pagina?>'>
                            <img src='../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/botao_encaminhar.png' title="" border='0'>
                        </a>
<?php
                    }

                    // Acao => ALTERACAO DE AGENDAMENTO DE MANUTENCAO
                    if ($sessao->acao == '105') {
                        $numPlaca= urlencode($numPlaca);

?>
     <a href='alteraAgendamento.php?<?=Sessao::getId();?>&codbem=<?=$codBemf;?>&empenho=<?=$empenho;?>&empenhoExercicio=<?=$empenhoExercicio;?>&data=<?=$val;?>&dataAntiga=<?=$val;?>&numPlaca=<?=$numPlaca;?>&nomNatureza=<?=$nomNatureza;?>&nomGrupo=<?=$nomGrupo;?>&nomEspecie=<?=$nomEspecie;?>&classificacao=<?=$classificacao;?>&ctrl=1'>
                            <img src='../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/btneditar.gif' title="" border='0'>
                        </a>
<?php
                    }

?>
                    </td>
                </tr>
<?php
        }
           }
          } else {
           echo "<tr><td colspan='6' class='show_dados_center'>Nenhum registro encontrado</td></tr>";
          }
?>
            </table>
<?php
            $conn->limpaSelecao();
            $conn->fechaBD();
?>
            <table width="100%" align="center">
            <tr>
                <td align="center"><font size=2>
                    <?=$paginacao->mostraLinks();?>
                </font></td>
            </tr>
            </table>
<?php
            exit();
        break;

    }
?>

<!-- FORMULARIO PADRAO PARA PESQUISA DE BENS CADASTRADOS -->
<?php
    unset ($sessao->transf);
?>
    <script type="text/javascript">
        // preenche os combos de Natureza, Grupo e Especie
        function preencheNGE(variavel, valor)
        {
            document.frm.target = "oculto";
            document.frm.controle.value = '0';
            document.frm.ctrl_frm.value = '1';
            document.frm.variavel.value = variavel;
            document.frm.valor2.value = valor;
            document.frm.submit();
        }

        // desabilita botao 'OK' se o valor informado no input text nao existir e vice-versa
        // submete o formulario para preencher os campos dependentes ao combo selecionado
        function verificaCombo(campo_a, campo_b)
        {
            var aux;
            aux = preencheCampo(campo_a, campo_b);
            if (aux == false) {
                document.frm.ok.disabled = true;
            } else {
                document.frm.ok.disabled = false;
            }
            preencheNGE(campo_b.name, campo_b.value)
        }

        // submete formulario
        function Salvar()
        {
            document.frm.target = "telaPrincipal";
            document.frm.ctrl_frm.value = '2';
            document.frm.submit();
        }

        function frmReset()
        {
            document.frm.reset();
            document.frm.codNatureza.focus();

            return(true);
        }

    </script>

    <form name='frm' method='post' action='<?=$action;?>?<?=Sessao::getId()?>' onreset="return frmReset();">

    <input type="hidden" name="controle" value=''>
    <input type="hidden" name="ctrl_frm" value=''>
    <input type="hidden" name="variavel" value=''>
    <input type="hidden" name="valor2" value=''>

    <table width="100%">
    <tr>
        <td class="alt_dados" colspan="2">Filtrar Bens</td>
    </tr>

    <tr>
        <td class="label" width="20%" title="Selecione a natureza do bem.">Natureza</td>
        <td class='field' width="80%">
        <!--
            <input type="text" name="codTxtNatureza"
                value="<?=$codNatureza != "xxx" ? $codNatureza : "";?>" size="10" maxlength="10"
                onChange="javascript: verificaCombo(this, document.frm.codNatureza);"
                onKeyPress="return(isValido(this, event, '0123456789'));">
        -->
            <select name='codNatureza' onChange="javascript: preencheNGE('codNatureza', this.value);" style="width:300px">
                <option value='xxx' SELECTED>Selecione</option>
<?php
                // busca Naturezas cadastradas
                $sSQL = "SELECT
                            cod_natureza, nom_natureza
                        FROM
                            patrimonio.natureza
                        ORDER
                            by nom_natureza";
                $dbEmp = new dataBaseLegado;
                $dbEmp->abreBD();
                $dbEmp->abreSelecao($sSQL);
                $dbEmp->vaiPrimeiro();

                // monta combo com Naturezas
                $comboCodNatureza = "";

                while (!$dbEmp->eof()) {

                    $codNaturezaf  = trim($dbEmp->pegaCampo("cod_natureza"));
                    $nomNaturezaf  = trim($dbEmp->pegaCampo("nom_natureza"));
                    $chave = $codNaturezaf;
                    $dbEmp->vaiProximo();

                    $comboCodNatureza .= "<option value='".$chave."'";

                    if (isset($codNatureza)) {
                        if ($chave == $codNatureza) {
                            $comboCodNatureza .= " SELECTED";
                            $nomNatureza = $nomNaturezaf;
                        }
                    }

                    $comboCodNatureza .= ">".$nomNaturezaf."</option>\n";
                }

                $dbEmp->limpaSelecao();
                $dbEmp->fechaBD();

                echo $comboCodNatureza;
?>
            </select>
            <input type="hidden" name="nomNatureza" value="">
        </td>
    </tr>

    <tr>
        <td class="label" width="20%" title="Selecione o grupo do bem.">Grupo</td>
        <td class="field">
        <!--
            <input type="text" name="codTxtGrupo"
                value="<?=$codGrupo != "xxx" ? $codGrupo : "";?>" size="10" maxlength="10"
                onChange="javascript: verificaCombo(this, document.frm.codGrupo);"
                onKeyPress="return(isValido(this, event, '0123456789'));">
        -->
            <select name="codGrupo" onChange="javascript: preencheNGE('codGrupo', this.value);" style="width:300px">
                <option value="xxx" SELECTED>Selecione</option>
            </select>

            <input type="hidden" name="nomGrupo" value="">
        </td>
    </tr>

    <tr>
        <td class="label" width="20%" title="Selecione a espécie do bem.">Espécie</td>
        <td class="field">
        <!--
            <input type="text" name="codTxtEspecie"
                value="<?=$codEspecie != "xxx" ? $codEspecie : "";?>" size="10" maxlength="10"
                onChange="javascript: verificaCombo(this, document.frm.codEspecie);"
                onKeyPress="return(isValido(this, event, '0123456789'));">
        -->
            <select name="codEspecie" onChange="javascript: preencheNGE('codEspecie', this.value);" style="width:300px" >
                <option value="xxx" SELECTED>Selecione</option>
            </select>

            <input type="hidden" name="nomEspecie" value="">
        </td>
    </tr>

    <tr>
        <td class="label" width="20%" title="Informe a descrição do bem.">Descrição</td>
        <td class="field">
             <input type="text" name="descricao" value="" size='80' maxlength="60">
        </td>
    </tr>

    <tr>
        <td class="label" width="20%" title="Informe o código do bem.">Código do Bem</td>
        <td class="field">
            <input type="text" name="codBem" value="" size='10' maxlength="8" onKeyUp="return autoTab(this, 8, event);" onKeyPress="return (isValido(this, event, '0123456789'));">
        </td>
    </tr>

    <tr>
        <td class="label" width="20%" title="Informe se o bem possui placa de identificação.">Placa de Identificação</td>
        <td class="field">
            <input type="radio" name="placaIdentificacao"  value="N" onFocus="document.frm.numPlaca.disabled = true; document.frm.stTipoBuscaDescricao.disabled = true;"
<?php
                if ($placaIdentificacao == 'N') {
                    echo " checked";
                }
?>
                >Não

                <input type="radio" name="placaIdentificacao" value="S" onFocus="document.frm.numPlaca.disabled = false; document.frm.stTipoBuscaDescricao.disabled = false;"
<?php
                if ($placaIdentificacao == 'S') {
                    echo " checked";
                }
?>
                >Sim

                <input type="radio" name="placaIdentificacao" value="T" checked  onFocus="document.frm.numPlaca.disabled = true; document.frm.stTipoBuscaDescricao.disabled = true;"
<?php
                if ($placaIdentificacao == 'T') {
                    echo " checked";
                }
?>
                >Todos

        </td>
    </tr>

    <tr>
        <td class="label" width="20%" title="Informe o número da placa do bem.">Número da Placa</td>
        <td class="field">
            <input type="text" name="numPlaca" value="" size='20' maxlength="20" onKeyUp="return autoTab(this, 20, event);" disabled>
            <select name="stTipoBuscaDescricao" tabindex="1" value="inicio" disabled>
                <option value="inicio" selected="selected">Início</option>
                <option value="final">Final</option>
                <option value="contem">Contém</option>
                <option value="exata">Exata</option>
            </select>

        </td>
    </tr>

    <tr>
        <td class="label" width="20%" title="Selecione o tipo de ordenação dos bens.">Ordenar por</td>
        <td class="field">
            <select name="order" style="width:200px">
                <option value="cod" SELECTED> Código
                <option value="desc"> Descrição
            </select>
        </td>
    </tr>

    <tr>
        <td colspan='2' class='field'>
            <?=geraBotaoOk();?>
        </td>
    </tr>

    </table>

    </form>
