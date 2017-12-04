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
    * Manutneção de usuários
    * Data de Criação: 25/07/2005

    * @author Analista: Cassiano
    * @author Desenvolvedor: Cassiano

    * Casos de uso: uc-01.03.93

    $Id: defineImpressoras.php 64287 2016-01-08 16:45:40Z diogo.zarpelon $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_FW_LEGADO.'usuarioLegado.class.php';
include_once CAM_FW_LEGADO.'paginacaoLegada.class.php';
include_once CAM_FW_LEGADO.'auditoriaLegada.class.php';
include_once CAM_FW_LEGADO.'mascarasLegado.lib.php';
include_once CAM_FW_LEGADO.'funcoesLegado.lib.php';
include_once CAM_FW_LEGADO.'dataBaseLegado.class.php';
include_once CAM_FW_LEGADO.'permissaoLegado.class.php';
include_once 'interfaceUsuario.class.php';

setAjuda( "UC-01.03.93" );

$controle = $request->get('controle');
$pg  = $request->get('pg');
$pos = $request->get('pos');

if (strlen($controle) == 0) {
    $controle = 0;
}
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
switch ($controle) {
case 0:
    $html = new interfaceUsuario;
    $ctrl = "altera";
    $html->formBuscaUsuario($ctrl,$PHP_SELF);
break;

case 1: // Escolhe um usuário para adicionar acesso às impressoras
    $condicao = "";

    $cpf = $_REQUEST['cpf'];
    $rg = $_REQUEST['rg'];
    $numCgm = $_REQUEST['numCgm'];
    $nomCgm = $_REQUEST['nomCgm'];
    $username = $_REQUEST['username'];
    $cnpj = $_REQUEST['cnpj'];

    if ($cpf != "" || $rg != "") {
        $condicao = ", sw_cgm_pessoa_fisica AS PF";
    }
    if ($cnpj != "") {
        $condicao = ", sw_cgm_pessoa_juridica AS PJ";
    }
    $sql  = "";
    $sql .= "   SELECT
                    C.numcgm as cgm,
                    C.nom_cgm,
                    U.username
                FROM
                    administracao.usuario as U,
                    sw_cgm as C
                    ".$condicao."
                WHERE
                    C.numcgm = U.numcgm ";
    if ($numCgm != "") {
        $sql .= " AND U.numcgm = ".$numCgm;
    }
    if ($nomCgm != "") {
        $sql .= " AND G.nom_cgm like '%".$nomCgm."%'";
    }
    if ($username != "") {
        $sql .= " AND U.username like '%".$username."%'";
    }
    if ($cpf != "") {
        $cpf = str_replace(".", "", $cpf);
        $cpf = str_replace("-", "", $cpf);
        $sql .= " AND PF.numcgm = C.numcgm";
        $sql .= " AND PF.cpf = ".$cpf;
    }
    if ($cnpj != "") {
        $cnpj = str_replace(".", "", $cnpj);
        $cnpj = str_replace("/", "", $cnpj);
        $cnpj = str_replace("-", "", $cnpj);
        $sql .= " AND PJ.numcgm = C.numcgm";
        $sql .= " AND PJ.cnpj = ".$cnpj;
    }
    if ($rg != "") {
        $sql .= " AND PF.numcgm = C.numcgm";
        $sql .= " AND PF.rg = ".$rg;
    }

    if (!Sessao::read('sql')) {
        Sessao::write('sql',$sql);
    }

    $obConexao = new Conexao;
    $obErro = $obConexao->executaSQL( $rsRecordSetPaginacao, $sql, $boTransacao);

    $obPaginacao = new Paginacao;
    $obPaginacao->setRecordSet( $rsRecordSetPaginacao );
    $obPaginacao->geraStrLinks();
    $obPaginacao->geraHrefLinks();
    $obPaginacao->montaHTML();
    
    # Monta a tabela de Paginação 
    $obTabelaPaginacao = new Tabela;
    $obTabelaPaginacao->addLinha();
    $obTabelaPaginacao->ultimaLinha->addCelula();

    $obTabelaPaginacao->ultimaLinha->ultimaCelula->setColSpan( $inNumDados + 2  );
    $obTabelaPaginacao->ultimaLinha->ultimaCelula->setClass('show_dados_center_bold');
    $obTabelaPaginacao->ultimaLinha->ultimaCelula->addConteudo("<font size='2'>".$obPaginacao->getHTML()."</font>" );
    $obTabelaPaginacao->ultimaLinha->commitCelula();
    $obTabelaPaginacao->commitLinha();
    $obTabelaPaginacao->addLinha();
    $obTabelaPaginacao->ultimaLinha->addCelula();

    $obTabelaPaginacao->ultimaLinha->ultimaCelula->setColSpan( $inNumDados + 2  );
    $obTabelaPaginacao->ultimaLinha->ultimaCelula->setClass('show_dados_center_bold');
    $obTabelaPaginacao->ultimaLinha->ultimaCelula->addConteudo("<font size='2'>Registros encontrados: ".$obPaginacao->getNumeroLinhas()."</font>" );
    $obTabelaPaginacao->ultimaLinha->commitCelula();
    $obTabelaPaginacao->commitLinha();
    $obTabelaPaginacao->montaHTML();

    $stHTMLPaginacao .= $obTabelaPaginacao->getHTML();

    $count = $obPaginacao->geraContador();
    
    # Monta o LIMIT + OFFSET para a consulta da listagem.
    $offset = 0;

    if ($_REQUEST['pg'] > 1) {
       $offset = ($_REQUEST['pg'] - 1) * 10;
    }

    $stOrderBy = " ORDER BY U.username ASC LIMIT 10 OFFSET $offset ";
    $sSQL = Sessao::read('sql').$stOrderBy;

?>
    <table width='100%' id="tabelas">
        <tr>
            <td class="alt_dados" colspan="5">
                Usuários disponíveis
            </td>
        </tr>
        <tr>
            <td class='labelcentercabecalho' width='5%'>&nbsp;</td>
            <td class='labelcentercabecalho' width='10%'>CGM</td>
            <td class='labelcentercabecalho' width='75%'>Nome</td>
            <td class='labelcentercabecalho' width='20%'>Usuário</td>
            <td class='labelcentercabecalho' width='5%'>&nbsp;</td>
        </tr>
<?php
    $conectaBD = new dataBaseLegado;
    $conectaBD->abreBD();
    $conectaBD->abreSelecao($sSQL);

    $conectaBD->vaiPrimeiro();
    while (!$conectaBD->eof()) {
        $cgm      = $conectaBD->pegaCampo("cgm");
        $nomCgm   = $conectaBD->pegaCampo("nom_cgm");
        $username = $conectaBD->pegaCampo("username");
?>
        <tr>
            <td class='show_dados_center_bold'><?=$count++?></td>
            <td class='show_dados_right'><?=$cgm;?></td>
            <td class='show_dados'><?=$nomCgm;?></td>
            <td class='show_dados'><?=$username;?></td>
            <td class='botao'>
                <a href="<?=$PHP_SELF;?>?<?=Sessao::getId();?>&controle=2&cgm=<?=$cgm;?>&username=<?=$username;?>&pagina=<?=$pagina;?>&pg=<?=$pg;?>&pos=<?=$pos;?>">
                    <img src="<?=CAM_FW_IMAGENS;?>botao_editar.png" border=0>
                </a>
            </td>
        </tr>
<?php
        $conectaBD->vaiProximo();
    }
    $conectaBD->limpaSelecao();
    $conectaBD->fechaBD();
?>
    </table>
<?php

 # Hack para nova paginação.
        echo "<table id='paginacao' width='850' align='center'>
                <tr>
                <td align='center'>
                <font size=2>";
        echo $stHTMLPaginacao;
        echo "</font></tr></td></table>";

    ?>
<script>zebra('tabelas','zb');</script>
<?php
    break;
case 2: // Mostra uma lista de impressoras a que o usuário pode ter acesso
    $usuario = new usuarioLegado;
    $cgm = $_REQUEST['cgm'];
    $username = $_REQUEST['username'];
?>
    <script type="text/javascript">
        function Salvar()
        {
            var boValidaPadrao = true;
            var erro = false;
            var mensagem = "";
            var campoID = 'impressoras_';
            if ( verificaImpressoraSelecionada() ) {
               if (document.frm.flagRadio.value) {
                   campoID += document.frm.flagRadio.value;
                   if ( !document.getElementById(campoID).checked ) {
                       erro = true
                       mensagem += "@A impressora padrão deve estar selecionada.";
                   }
               }
            }
            if (document.frm.flagRadio.value) {
                boValidaPadrao = false;
            }
            if (boValidaPadrao) {
                mensagem += "@Deve haver uma impressora selecionada como padrão.";
                erro = true;
            }
            if (erro) {
                alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
            } else {
                document.frm.submit();
            }
        }

        function verificaImpressoraSelecionada()
        {
            var boRetorno = false;
            var stCheckId = "impressoras_";
            for (var i = 1; i <= document.frm.inQtdImpressoras.value; i++) {
                 var stTmpId = stCheckId + i;
                 if ( document.getElementById(stTmpId).checked ) {
                     boRetorno = true;
                     break;
                 }
            }

            return boRetorno;
        }

        function setCampoID(valor)
        {
            document.frm.flagRadio.value = valor;
        }

        function Cancela()
        {
            document.frm.action = "<?=$PHP_SELF;?>?<?=Sessao::getId();?>&controle=0";
            document.frm.submit();
        }

        function verificaRadio(id)
        {
            chck = document.getElementById('impressoras_'+id);
            if (chck.checked == false) {
                    radio = document.getElementById('radio_'+id);
                    radio.checked = false;
            }
            }

            function marcaCheckBox(id,idImpressora)
            {
                    chck = document.getElementById('impressoras_'+id);
            radio = document.getElementById('radio_'+id);
            if (radio.checked == true) {
                    chck.checked = true;
                    chck.value = idImpressora;
            }
        }

    </script>
    <form name="frm" action="<?=$PHP_SELF;?>?<?=Sessao::getId();?>&controle=3" method="POST">
        <input type="hidden" name="cgm" value='<?=$cgm;?>'>
        <input type="hidden" name="username" value='<?=$username;?>'>
        <input type="hidden" name="pagina" value='<?=$pagina;?>'>
        <table width='100%'>
            <tr>
                <td class='alt_dados' width='2%'>&nbsp;</td>
                <td class='alt_dados' width='48%'>Impressora</td>
                <td class='alt_dados' width='50%'>Localização</td>
                <td class='alt_dados' width='50%'>Impressora Padrão</td>
            </tr>
        <?php
            $sSQL = "   SELECT impressora.cod_impressora,
                               impressora.nom_impressora,
                               impressora.cod_orgao,
                               impressora.cod_local,
                               local.descricao as nom_local
                         FROM  administracao.impressora

                   INNER JOIN  organograma.local
                           ON  local.cod_local = impressora.cod_local

                        WHERE  impressora.cod_impressora > 0
                     ORDER BY  nom_impressora ";

            $conectaBD = new dataBaseLegado;
            $conectaBD->abreBD();
            $conectaBD->abreSelecao($sSQL);
            $conectaBD->fechaBD();
            $conectaBD->vaiPrimeiro();
            while (!$conectaBD->eof()) {
                    $codImpressora = $conectaBD->pegaCampo("cod_impressora");
                    $nomLocal      = $conectaBD->pegaCampo("nom_local");
        ?>
            <tr>
                <?php
                    if ($usuario->verificaUsuarioImpressora($cgm,$conectaBD->pegaCampo("cod_impressora"))) {
                        $stChecked = "checked";
                    } else {
                        $stChecked = "";
                    }
                    $inContPadrao++;
                ?>
                <td class="label"><input type="checkbox" name="impressoras[]" value='<?=$codImpressora;?>' id="impressoras_<?=$inContPadrao;?>" <?=$stChecked?> onClick="verificaRadio(<?=$inContPadrao;?>);"></td>
                <td class="field"><?=$conectaBD->pegaCampo("nom_impressora");?></td>
                <td class="field"><?=$nomLocal?></td>
                <?php
                $select = 	"SELECT impressora_padrao FROM administracao.usuario_impressora WHERE cod_impressora = ".$codImpressora." AND numcgm = ".$cgm;
                $dbConfig = new databaseLegado;
                $dbConfig->abreBd();
                $dbConfig->abreSelecao($select);
                $checked = $dbConfig->pegaCampo("impressora_padrao");
                $dbConfig->limpaSelecao();
                $dbConfig->fechaBd();

                //CASSIANO 09/09/2004
                if ($checked == "t") {
                    $stChkPadrao = "checked";
                    $inValorFlagRadio = $inContPadrao;
                } else {
                    $stChkPadrao = "";
                }
                //FIM CASSIANO

                ?>
                <td class="field"><input type="radio" name="padrao" <?=$stChkPadrao;?> value="<?=$codImpressora?>" id="radio_<?=$inContPadrao;?>" onChange="javascript: setCampoID(<?=$inContPadrao;?>);" onClick="marcaCheckBox(<?=$inContPadrao;?>,<?=$codImpressora;?>);"></td>
            </tr>
        <?php   $conectaBD->vaiProximo();
                }
            $conectaBD->limpaSelecao();

        ?>
            <tr>
                <td colspan="4" class='field'>
                    <input type="hidden" name="inQtdImpressoras" value="<?=$inContPadrao;?>">
                    <input type="hidden" name="flagRadio" value="<?=$inValorFlagRadio;?>">
                    <?=geraBotaoOk(1,0,1,1);?>
                </td>
            </tr>
        </table>
    </form>

<?php
    break;

    case 3: //Inclui o acesso às impressoras selecionadas para o usuário

        $usuario = new usuarioLegado;
        $username = $_REQUEST['username'];
        $cgm = $_REQUEST['cgm'];
        $padrao = $_REQUEST['padrao'];
        $impressoras = $_REQUEST['impressoras'];
        $inQtdImpressoras = $_REQUEST['inQtdImpressoras'];
        $flagRadio = $_REQUEST['flagRadio'];
        
        if ($usuario->incluiUsuarioImpressora($cgm,$impressoras,$padrao)) {
            //Inclui auditoria
            $audicao = new auditoriaLegada;
            $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $username);
            $audicao->insereAuditoria();
            //Exibe mensagens
            if (count($impressoras) > 0) {                
                SistemaLegado::alertaAviso($PHP_SELF,"Acesso às impressoras incluído com sucesso para o usuário $username!","unica","aviso");
            } else {
                    SistemaLegado::alertaAviso($PHP_SELF,"Nenhuma impressora definida para o usuário $username","unica","aviso");
            }
        } else {
            SistemaLegado::alertaAviso($PHP_SELF,"Foi retirado o acesso a(s) impressora(s) para o usuário $username","unica","aviso");
        }
    break;
}// Fim switch($controle)

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
