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
* Arquivo de instância para manutenção de CGM
* Data de Criação: 27/02/2003

* @author Analista:
* @author Desenvolvedor: Ricardo Lopes de Alencar

* @package URBEM
* @subpackage

* Casos de uso: uc-01.02.92, uc-01.02.93

  $Id: interfaceCgm.class.php 64287 2016-01-08 16:45:40Z diogo.zarpelon $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once (CAM_FW_LEGADO."funcoesLegado.lib.php"    );
include_once (CAM_FW_LEGADO."paginacaoLegada.class.php");
include_once (CAM_FW_LEGADO."paginacaoLegada.class.php");
include_once (CAM_GA_ADM_MAPEAMENTO."TOrgaoRegistro.class.php" );
class interfaceCgm
{
    /** Método Construtor **/
    function interfaceCgm()
    {
        $dadosCgm = "";
    }//Fim do método construtor

/**************************************************************************
 Gera o Combo com os tipos de vínculo de um processo
/**************************************************************************/
    function comboTipoCgm($nome="pessoa",$default="",$espec="")
    {
        $vetVinculo["fisica"] = "Cadastro de Pessoa Física";
        $vetVinculo["juridica"] = "Cadastro de Pessoa Jurídica";
        //$vetVinculo[outros] = "Cadastro Geral";
            $combo = "";
            $combo .= "<select name='".$nome."' ".$espec." style='width: 300px'>\n";
                if($default=="")
                    $combo .= "<option value='xxx' selected>Selecione um tipo de cadastro</option>\n";
            foreach ($vetVinculo as $chave=>$valor) {
                $selected = "";
                    if($chave==$default)
                        $selected = "selected";
                $combo .= "<option value='".$chave."' ".$selected.">".$valor."</option>\n";
            }
            $combo .= "</select>";

        return $combo;
    }//Fim function comboTipoCgm

/***************************************************************************
Mostra as opções para inclusão dee CGM:
Pessoa física, jurídica ou outros
/**************************************************************************/
    function listaTipos()
    {
?>
        <br>
        <b>Escolha que tipo de CGM deseja incluir:</b>
        <br><br>
        <table width="75%">
            <tr>
                <td class='fieldcenter' style='font-weight: bold;'>
                    <a href="<?=$PHP_SELF;?>?<?=Sessao::getId();?>&controle=2&pessoa=fisica">Incluir novo cadastro Pessoa Física</a>
                </td>
            </tr>
        </table>
        <br>
        <table width="75%">
            <tr>
                <td class='fieldcenter' style='font-weight: bold;'>
                    <a href="<?=$PHP_SELF;?>?<?=Sessao::getId();?>&controle=2&pessoa=juridica">Incluir novo cadastro Pessoa Jurídica</a>
                </td>
            </tr>
        </table>
        <br>
        <table width="75%">
            <tr>
                <td class='fieldcenter' style='font-weight: bold;'>
                    <a href="<?=$PHP_SELF;?>?<?=Sessao::getId();?>&controle=2&pessoa=outros">Incluir novo cadastro </a>
                </td>
            </tr>
        </table>
<?php
    }//Fim function listaTipos

/**************************************************************************/
/**** Gera o Combo com os tipos de logradouro para seleção              ***/
/**************************************************************************/
    public function listaTipoLogradouro($nome="tipoLogradouro",$default="")
    {
        $sql = "SELECT nom_tipo FROM sw_tipo_logradouro ORDER by nom_tipo";
        //echo "<!--".$sql."-->";
        $dataBase = new dataBaseLegado;
        $dataBase->abreBD();
        $dataBase->abreSelecao($sql);
        $dataBase->vaiPrimeiro();
            $combo = "";
            $combo .= "<select name='".$nome."' style='width:200px'>";
            while (!$dataBase->eof()) {
                $nomLogradouro = trim($dataBase->pegaCampo("nom_tipo"));
                $selected = "";
                    if($nomLogradouro==$default)
                        $selected = "selected";
                $dataBase->vaiProximo();
                $combo .= "<option value='".$nomLogradouro."' ".$selected.">".$nomLogradouro."</option>";
            }
            if ($default=="") {
                $selected = "selected";
            } else {
                $selected = "";
            }
            //$combo .= "<option value='' ".$selected.">Não Informado</option>";
            $combo .= "</select>";
        $dataBase->limpaSelecao();
        $dataBase->fechaBD();

        return $combo;
    }//Fim function listaTipoLogradouro

/***************************************************************************
Monta o campo atritbuto conforme o tipo
/**************************************************************************/
    public function montaAtributo($codAtributo, $tipo, $valorPadrao="", $valor = "")
    {
        $sHTML = "";
        switch ($tipo) {
            case 't':
                $sHTML .= "<input type=\"text\" name=\"atributo[".$codAtributo."]\"";
                if ($valor != NULL) {
                    $sHTML .= " value=\"$valor\">";
                } else {
                    $sHTML .= " value=\"$valorPadrao\">\n";
                }
            break;
            case 'n':
                $sHTML .= "<input type=\"text\" name=\"atributo[".$codAtributo."]\"";
                if ($valor != NULL) {
                    $sHTML .= " value=\"$valor\"";
                } else {
                    $sHTML .= " value=\"$valorPadrao\"";
                }
                $sHTML .= " onKeyPress=\"return(isValido(this, event, '0123456789'));\">\n";
            break;
            case 'l':
                $arSelect = explode("\n", $valor);
                $sHTML .= "<select name=\"atributo[".$codAtributo."]\">' style=\"width: 300px\"\n";
                foreach ($arSelect as $option) {
                    if ($option == $valor) {
                        $sHTML .= "    <option value=\"".$option."\" selected>".$option."</option>\n";
                    } else {
                        $sHTML .= "    <option value=\"".$option."\">".$option."</option>\n";
                    }
                }
                $sHTML .= "</option>\n";
            break;
        }

        return $sHTML;
    }

/***************************************************************************
Monta o formulário de busca por CGM
/**************************************************************************/
    public function formBuscaCgm($action="",$ctrl=0,$formAcao="")
    {
?>
<script type="text/javascript">

    //A função Valida() faz a verfificação dos campos, monte-a conforme a sua necessidade.
    function Valida()
    {
        var mensagem = "";
        var erro = false;
        var campo;
        var campo1;
        var campo2;
        var campo3;
        var campo4;
        var campo5;
        var campoaux;

        campo1 = document.frm.numCgm.value.length;
        campo2 = document.frm.nomCgm.value.length;
        campo3 = document.frm.cpf.value.length;
        campo4 = document.frm.cnpj.value.length;
        campo5 = document.frm.rg.value.length;

        //Não executar a busca sem que haja pelo menos um parâmetro informado
        if (campo1==0 && campo2==0 && campo3==0 && campo4==0 && campo5==0) {
            mensagem += "@Informe pelo menos um parâmetro de busca";
            erro = true;
        }

        //Somente um dos campos pode ser preenchido
        if ((campo1>0 && campo2>0) || (campo1>0 && campo3>0) || (campo1>0 && campo4>0) || (campo2>0 && campo3>0) || (campo2>0 && campo4>0) || (campo3>0 && campo4>0) ) { //Verificação tipo XOR
            mensagem += "@Informe apenas um parâmetro de busca";
            erro = true;
        }

        campo = document.frm.numCgm.value;
        if (isNaN(campo)) {
            mensagem += "@Campo CGM inválido!("+campo+")";
            erro = true;
        }

        if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
        return !(erro);
    }// Fim da function Valida

    //A função salvar testa a validação, e se tudo ocorrer certo, envia o form
    function Salvar()
    {
        if (Valida()) {
            document.frm.submit();
        }
    }

</script>
    <form name="frm" action="<?=$action;?>?<?=Sessao::getId();?>" method="POST">
<?php

if ($formAcao == "CGA") {
    echo "            <input type='hidden' name='controle' value='".$ctrl."'>\n";
} else {
    echo "            <input type='hidden' name='controle' value='2'>\n";
}
?>

    <table width="100%">
        <tr>
            <td class=alt_dados colspan=2>Dados para filtro</td>
        </tr>
        <tr>
<?php
if ($formAcao == "CGA") {
    echo "            <td class=\"label\" width=\"30%\" title='Informe o CGA.'>CGA</td>\n";
} else {
    echo "            <td class=\"label\" width=\"30%\" title='Informe o CGM.'>CGM</td>\n";
}
?>

            <td class="field">
            <input type="text" class="field" name="numCgm" size="10" maxlength="15" value=""
        onKeyUp="JavaScript:return autoTab(this, 10, event);"
        onKeyPress="return(isValido(this, event, '0123456789'));"
        >&nbsp;
            </td>
        </tr>
        <tr>
            <td class="label" title="Informe o nome ou razão social.">Nome/Razão Social</td>
            <td class="field">
                <input type="text" name="nomCgm" size="30" maxlength="200" value="" onKeyUp="JavaScript:return autoTab(this, 200, event);">
                <select name='tipoBusca'>
                    <option value='inicio'>Início</option>
                    <option value='final'>Final</option>
                    <option value='contem'>Contém</option>
                    <option value='exata'>Exata</option>
                </select>
            </td>
        </tr>
        <tr>
            <td class="label" title="Informe o CNPJ.">CNPJ</td>
            <td class="field">
                <input type="text" name="cnpj" maxlength="18" size="22" value=""
        onKeyUp="JavaScript: mascaraCNPJ( this, event );return autoTab(this, 18, event);"
        onKeyPress="return(isValido(this, event, '0123456789'));">&nbsp;
            </td>
        </tr>
        <tr>
            <td class="label" title="Informe o CPF.">CPF</td>
            <td class="field">
                <input type="text" maxlength="14" name="cpf" size="14" value=""
        onKeyUp="JavaScript: mascaraCPF( this, event );return autoTab(this, 14, event);"
        onKeyPress="return(isValido(this, event, '0123456789'));">&nbsp;
            </td>
        </tr>
        <tr>
            <td class="label" title="Informe o RG.">RG</td>
            <td class="field">
                <input type="text" maxlength="15" name="rg" size="15" value=""
        onKeyUp="JavaScript: return autoTab(this, 15, event);"
        onKeyPress="return(isValido(this, event, '0123456789'));">&nbsp;
            </td>
        </tr>
        <tr>
            <td colspan="2" class='field'>
                <?php geraBotaoOk();?>
            </td>
        </tr>
    </table>
    </form>
    <script type='text/javascript'>
    <!--
        document.frm.numCgm.focus();
    //-->
    </script>
<?php
    }// Fim function formBuscaCgm

/***************************************************************************
Monta o formulário de busca por CGM
/**************************************************************************/
    public function formBuscaCgmConverte($action="",$ctrl=0,$formAcao="")
    {
?>
<script type="text/javascript">

    //A função Valida() faz a verfificação dos campos, monte-a conforme a sua necessidade.
    function Valida()
    {
        var mensagem = "";
        var erro = false;
        var campo;
        var campo1;
        var campo2;
        var campoaux;

        campo1 = document.frm.numCgm.value.length;
        campo2 = document.frm.nomCgm.value.length;

        //Não executar a busca sem que haja pelo menos um parâmetro informado
        if (campo1==0 && campo2==0) {
            mensagem += "@Informe pelo menos um parâmetro de busca";
            erro = true;
        }

        //Somente um dos campos pode ser preenchido
        if ((campo1>0 && campo2>0)) { //Verificação tipo XOR
            mensagem += "@Informe apenas um parâmetro de busca";
            erro = true;
        }

        campo = document.frm.numCgm.value;
        if (isNaN(campo)) {
            mensagem += "@Campo CGM inválido!("+campo+")";
            erro = true;
        }

        if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
        return !(erro);
    }// Fim da function Valida

    //A função salvar testa a validação, e se tudo ocorrer certo, envia o form
    function Salvar()
    {
        if (Valida()) {
            document.frm.submit();
        }
    }

</script>
    <form name="frm" action="<?=$action;?>?<?=Sessao::getId();?>" method="POST">
    <input type="hidden" name="controle" value="2">

    <table width="100%">
        <tr>
            <td class=alt_dados colspan=2>Dados para filtro</td>
        </tr>
        <tr>
<?php
if ($formAcao == "CGA") {
    echo "            <td class=\"label\" width=\"30%\">CGA</td>\n";
} else {
    echo "            <td class=\"label\" width=\"30%\">CGM</td>\n";
}
?>

            <td class="field">
            <input type="text" class="field" name="numCgm" size="10" maxlength="15" value=""
        onKeyUp="JavaScript:return autoTab(this, 10, event);"
        onKeyPress="return(isValido(this, event, '0123456789'));">&nbsp;
            </td>
        </tr>
        <tr>
            <td class="label">Nome/Razão Social</td>
            <td class="field">
            <input type="text" name="nomCgm" size="30" maxlength="200" value=""
        onKeyUp="JavaScript:return autoTab(this, 30, event);">
                <select name='tipoBusca'>
                    <option value='inicio'>Início</option>
                    <option value='final'>Final</option>
                    <option value='contem'>Contém</option>
                    <option value='exata'>Exata</option>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2" class='field'>
                <?php geraBotaoOk();?>
            </td>
        </tr>
    </table>
    </form>
    <script type='text/javascript'>
    <!--
        document.frm.numCgm.focus();
    //-->
    </script>
<?php
    }// Fim function formBuscaCgmConverte

/***************************************************************************
Monta uma lista com os CGM's encontrados pela busca
Recebe os dados em forma de matriz
/**************************************************************************/
    public function exibeBusca($sSQL,$param="", $formulario = "cgm", $ctrl = 2)
    {
        $stSqlPaginacao = $sSQL;

        if ($_GET['paginando']) {
            $sSQL = Sessao::read('stSql');
        }

        if ( !isset( $_GET['pagina'] ) ) {
            $_GET['pagina'] = 0;
        }
        
        Sessao::write('stSql', $sSQL);
        
        $obConexao = new Conexao;
        $obErro = $obConexao->executaSQL( $rsRecordSetPaginacao, $stSqlPaginacao, $boTransacao);

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

        # Monta o LIMIT + OFFSET para a consulta.
        $offset = 0;

        if ($_REQUEST['pg'] > 1) {
           $offset = ($_REQUEST['pg'] - 1) * 10;
        }

        $stOrderBy = " ORDER BY lower(C.nom_cgm) ASC LIMIT 10 OFFSET $offset ";
        $sSQL = $sSQL.$stOrderBy;

        //print $sSQL;
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();

        $cont = $obPaginacao->geraContador();
?>
        <script type="text/javascript">
            function excluirCgm(cgm)
            {
                var bem = cgm;
                var objeto = cgm;
                alertaQuestao('<?=CAM_CGM."cgm/manutencao/excluiCgm.php?".Sessao::getId();?>','excluir',bem+'%26pagina=<?=$_GET["pagina"];?>',objeto,'sn_excluir','<?=Sessao::getId();?>&pagina=<?=$_GET["pagina"]?>&volta=true&controle=2');
            }

            function zebra(id, classe)
            {
                var tabela = document.getElementById(id);
                var linhas = tabela.getElementsByTagName("tr");
                    for (var i = 0; i < linhas.length; i++) {
                    ((i%2) == 0) ? linhas[i].className = classe : void(0);
                }
            }
        </script>
        <table width='100%' id="processos">
            <tr>
            <td class=alt_dados colspan=4>Registros de <?=strtoupper($formulario);?></td>
        </tr>
<?php
        $msg = urlencode("Nenhum registro encontrado! Deseja fazer uma busca por CGA?");
        if ( $dbEmp->eof() ) {
            if ($_GET['volta'] == 'true') {
                echo    "<script type='text/javascript'>
                            mudaTelaPrincipal('".$_SERVER['PHP_SELF']."?".Sessao::getId()."');
                        </script>";
            } elseif ( $dbEmp->eof() and $formulario != "cga" ) {
               echo '<script type="text/javascript">
                       alertaQuestao("'.CAM_CGM.'cgm/manutencao/buscaCga.php?'.Sessao::getId().'&stDescQuestao='.$msg.'","controle","'.$ctrl.'","","unica","'.Sessao::getId().'");
                      document.location = "'.$_SERVER['PHP_SELF'].'?'.Sessao::getId().'";
                      </script>';

            } else {
                echo "<tr><td class=fieldcenter colspan=4>Nenhum registro encontrado!</td></tr>";
            }
        } else {
?>
            <tr>
                <td class=labelleftcabecalho width=5%>&nbsp;</td>
                <td class=labelleftcabecalho width=12%>CGM</td>
                <td class=labelleftcabecalho width=80%>Nome/Razão Social</td>
                <td class=labelleftcabecalho>&nbsp;</td>
            </tr>
<?php

        while (!$dbEmp->eof()) {
?>
                    <tr>
                <td class=show_dados_center_bold><?=$cont++;?></td>
                        <td class="show_dados">
                            <?=$dbEmp->pegaCampo('numcgm');?>
                        </td>
                        <td class="show_dados">
                            <?=stripslashes($dbEmp->pegaCampo('nom_cgm'));?>
                        </td>
                        <td class="botao">
<?php

if ($param=='excluir') {
?>
                        <a href="javascript:excluirCgm(<?=$dbEmp->pegaCampo('numcgm');?>);"><img src="<?=CAM_FW_IMAGENS."btnexcluir.gif";?>" border=0></a>
<?php
} elseif ($param=='alterar') {
?>
                         <a href='<?=$PHP_SELF;?>?<?=Sessao::getId();?>&controle=3&numCgm=<?=$dbEmp->pegaCampo('numcgm');?>&nomCgm=<?=$dbEmp->pegaCampo('nom_cgm');?>&timestamp=<?=$dbEmp->pegaCampo('timestamp');?>&pagina=<?=$_GET['pagina']?>'><img src="<?=CAM_FW_IMAGENS."btneditar.gif";?>" height=20 border=0></a>
<?php
} else {
?>
                 <a href='<?=$PHP_SELF;?>?<?=Sessao::getId();?>&controle=3&numCgm=<?=$dbEmp->pegaCampo('numcgm');?>&nomCgm=<?=$dbEmp->pegaCampo('nom_cgm');?>&timestamp=<?=$dbEmp->pegaCampo('timestamp');?>&pagina=<?=$_GET['pagina']?>&pg=<?=$_GET['pg']?>&pos=<?=$_GET['pos']?>'><img src="<?=CAM_FW_IMAGENS."btneditar.gif";?>" height=20 border=0></a>
<?php
 }
?>
                        </td>
                    </tr>
<?php
            $dbEmp->vaiProximo();
            }
        }

        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
        echo $exec;

        # Hack para nova paginação.
        echo "<table id='paginacao' width='850' align='center'>
                <tr>
                <td align='center'>
                <font size=2>";
        echo $stHTMLPaginacao;
        echo "</font></tr></td></table>";
?>
        <script>zebra('processos','zb');</script>
<?php
    
    }//Fim da function exibeBusca



function formCGMPessoaJuridica($dadosCgm, $boInterno = false)
{
    if (is_array($dadosCgm)) {
         foreach ($dadosCgm as $campo=>$valor) {
             if (is_array($valor)) {
        foreach ($valor as $campo=>$valores) {
            $campo = htmlspecialchars(trim($valores));
        }
         } else {
        $$campo = htmlspecialchars(trim($valor));
         }
         }
    }
    $chObrigatorio = "*";
    if ($boInterno) {
        $chObrigatorio = "";
    }

    //Carrega o cnpj em partes para preencher os campos segmentados
    if (isset($cnpj)) {
    if ($cnpj) {
            $cnpj1 = substr($cnpj,0,2);
            $cnpj2 = substr($cnpj,2,3);
            $cnpj3 = substr($cnpj,5,3);
            $cnpj4 = substr($cnpj,8,4);
            $cnpj5 = substr($cnpj,12,2);
        $cnpj = $cnpj1.".".$cnpj2.".".$cnpj3."/".$cnpj4."-".$cnpj5;
        }
    }
    $stCheckTipoAlteracaoCorrecao = "checked";
    $stCheckTipoAlteracaoAlteracao = "";
    if ( isset($stTipoAlteracao) && ($stTipoAlteracao == "alteracao") ) {
        $stCheckTipoAlteracaoCorrecao = "";
        $stCheckTipoAlteracaoAlteracao = "checked";
    }

if ( !empty($numCgm ) ) {
$stHTML = <<<HEREDOC
                Correção <input type='radio' name='stTipoAlteracao' value='correcao' $stCheckTipoAlteracaoCorrecao>
                Alteração <input type='radio' name='stTipoAlteracao' value='alteracao' $stCheckTipoAlteracaoAlteracao onChange='if (this.checked == true) { document.frm.nomCgm.value = "";}document.frm.nomCgm.focus();'>
HEREDOC;
}

    $nomFantasia = isset($nomFantasia) ? $nomFantasia : "";
    $nomCgm = isset($nomCgm) ? $nomCgm : "";
    $cnpj = isset($cnpj) ? $cnpj  : "" ;
    $inscEst = isset($inscEst) ? $inscEst : "";
    $cod_orgao_registro = isset($cod_orgao_registro) ? $cod_orgao_registro : "";
    $num_registro = isset($num_registro) ? $num_registro : "";
    $dt_registro = isset($dt_registro) ? $dt_registro : "";
    $num_registro_cvm = isset($num_registro_cvm) ? $num_registro_cvm : "";
    $dt_registro_cvm = isset($dt_registro_cvm) ? $dt_registro_cvm : "";
    $objeto_social = isset($objeto_social) ? $objeto_social : "";
    
    $obOrgaoRegistro = new TOrgaoRegistro;
    $obOrgaoRegistro->recuperaTodos($rsOrgaoRegistro);
    //$arOrgaoRegistro = $rsOrgaoRegistro->arElementos;
    
    $obCmbOrgaoRegistro = new Select;
    $obCmbOrgaoRegistro->setRotulo       ("Orgao Registro"        );
    $obCmbOrgaoRegistro->setId           ("cmbOrgao"              );
    $obCmbOrgaoRegistro->setName         ("cmbOrgao"              );
    $obCmbOrgaoRegistro->setCampoId      ("codigo"                );
    $obCmbOrgaoRegistro->setCampoDesc    ("[codigo] - [descricao]");
    $obCmbOrgaoRegistro->preencheCombo   ($rsOrgaoRegistro        );
    $obCmbOrgaoRegistro->setValue        ($cod_orgao_registro     );
    $obCmbOrgaoRegistro->setNull         (true                    );
    $obCmbOrgaoRegistro->setStyle        ("width: 220px"          );
    
    $obTxtDataRegistro = new Data;
    $obTxtDataRegistro->setRotulo        ( "Data do Órgão Registro"           );
    $obTxtDataRegistro->setName          ( "stDataRegistro"                   );
    $obTxtDataRegistro->setId            ( "stDataRegistro"                   );
    $obTxtDataRegistro->setValue         ( $dt_registro                       );
    $obTxtDataRegistro->setNull          ( true                               );
    $obTxtDataRegistro->setTitle         ( "Informe a data do Órgão Registro" );
    
    $obNumRegistro = new Inteiro;
    $obNumRegistro->setRotulo        ( "Número do Registro do Órgão"           );
    $obNumRegistro->setTitle         ( "Informe o número do registro do órgão" );
    $obNumRegistro->setName          ( "inNumRegistro"                         );
    $obNumRegistro->setId            ( "inNumRegistro"                         );
    $obNumRegistro->setValue         ( $num_registro                           );
    $obNumRegistro->setNegativo      ( false                                   );
    $obNumRegistro->setNull          ( true                                    );
    $obNumRegistro->setMaxLength     ( 20                                      );
    
    $obNumCVM = new Inteiro;
    $obNumCVM->setRotulo        ( "Número do Registro CVM"                                          );
    $obNumCVM->setTitle         ( "Informe o número do registro da Comissão de Valores Mobiliários" );
    $obNumCVM->setName          ( "inNumCVM"                                                        );
    $obNumCVM->setId            ( "inNumCVM"                                                        );
    $obNumCVM->setValue         ( $num_registro_cvm                                                 );
    $obNumCVM->setNegativo      ( false                                                             );
    $obNumCVM->setNull          ( true                                                              );
    $obNumCVM->setMaxLength     ( 20                                                                );
    
    $obTxtDataCVM = new Data;
    $obTxtDataCVM->setRotulo        ( "Data do Registro CVM"             );
    $obTxtDataCVM->setName          ( "stDataRegistroCVM"                );
    $obTxtDataCVM->setId            ( "stDataRegistroCVM"                );
    $obTxtDataCVM->setValue         ( $dt_registro_cvm                   );
    $obTxtDataCVM->setNull          ( true                               );
    $obTxtDataCVM->setTitle         ( "Informe a data do Órgão Registro" );
    
    $obSocial = new TextArea;
    $obSocial->setRotulo        ( "Objeto Social"                        );
    $obSocial->setTitle         ( "Informe a descrição do objeto social" );
    $obSocial->setName          ( "stOjetoSocial"                        );
    $obSocial->setId            ( "stOjetoSocial"                        );
    $obSocial->setValue         ( $objeto_social                         );
    $obSocial->setNull          ( true                                   );
    
    $obCmbOrgaoRegistro->montaHTML();
    $obTxtDataRegistro->montaHTML();
    $obNumCVM->montaHTML();
    $obTxtDataCVM->montaHTML();
    $obSocial->montaHTML();
    $obNumRegistro->montaHTML();
    
    $stHtmlOrgaoRegistro = $obCmbOrgaoRegistro->getHtml();
    $stHtmlDataRegistro = $obTxtDataRegistro->getHtml();
    $stHtmlNumCVM = $obNumCVM->getHtml();
    $stHtmlDataCVM = $obTxtDataCVM->getHtml();
    $stHtmlObjSocial = $obSocial->getHtml();
    $stHtmlNumRegistro = $obNumRegistro->getHtml();
    
?>

        <tr>
            <td class="label" width="30%" title="Informe a razão social do CGM.">*Razão Social</td>
            <td class="field" width="70%">
                <input type="text" name="nomCgm" maxlength="200" size="50" value="<?=htmlspecialchars_decode($nomCgm)?>" onKeyUp="return autoTab(this, 200, event);" >
             </td>
        </tr>
<?php
$stHTML = <<<HEREDOC

        <tr>
            <td class="label" title="Informe o nome fantasia da empresa.">{$chObrigatorio}Nome Fantasia</td>
            <td class="field">
                <input type="text" name="nomFantasia" maxlength="200" size="50" value="$nomFantasia">
            </td>
        </tr>
        <tr>
            <td class="label" title="Informe o CNPJ.">{$chObrigatorio}CNPJ</td>
            <td class="field">
                <input type="text" name="cnpj" maxlength="18" size="22" value="$cnpj"
                onKeyUp="JavaScript: mascaraCNPJ(this, event);"
                onKeyPress="return(isValido(this, event, '0123456789'));"
                onChange  ="JavaScript: buscaValor( 600);">
            </td>
        </tr>
        <tr>
            <td class="label" title="Informe a inscrição estadual.">Inscrição estadual</td>
            <td class="field"><input type="text" name="inscEstadual" maxlength="14" size="14" value="$inscEst"
            onKeyUp="return autoTab(this, 14, event);" onkeypress="return validaExpressao( this, event, '[0-9]' );" ></td>
        </tr>
        
        <tr>
            <td class="label" title="Informe o registro do órgão.">Orgão do Registro</td>
            <td class="field">$stHtmlOrgaoRegistro</td>
        </tr>
        
        <tr>
            <td class="label" title="Informe o número do registro do órgão">Número do Registro do Órgão</td>
            <td class="field">$stHtmlNumRegistro</td>
        </tr>
        
         <tr>
            <td class="label" title="Informe a data do órgão registro">Data do Órgão Registro</td>
            <td class="field">$stHtmlDataRegistro</td>
        </tr>
        
        <tr>
            <td class="label" title="Informe o número do registro da Comissão de Valores Mobiliários">Número do Registro CVM</td>
            <td class="field">$stHtmlNumCVM</td>
        </tr>
        
        <tr>
            <td class="label" title="Informe a data da Comissão de Valores Mobiliários">Data CVM</td>
            <td class="field">$stHtmlDataCVM</td>
        </tr>
        
        <tr>
            <td class="label" title="Informe a descrição do objeto social">Objeto Social</td>
            <td class="field">$stHtmlObjSocial</td>
        </tr>        
HEREDOC;
return $stHTML;
}


function formCGMPessoaFisica($dadosCgm, $boInterno = false)
{
    if (is_array($dadosCgm)) {
         foreach ($dadosCgm as $campo=>$valor) {
             if (is_array($valor)) {
        foreach ($valor as $campo=>$valores) {
            $$campo = trim($valores);
        }
         } else {
        $$campo = trim($valor);
         }
         }
    }

    $chObrigatorio = "*";
    if ($boInterno) {
        $chObrigatorio = "";
    }

    //Carrega o cpf em partes para preencher os campos segmentados
    if (isset($cpf)) {
        if ($cpf) {
            $cpf1 = substr($cpf,0,3);
            $cpf2 = substr($cpf,3,3);
            $cpf3 = substr($cpf,6,3);
            $cpf4 = substr($cpf,9,2);
            $cpf = $cpf1.".".$cpf2.".".$cpf3."-".$cpf4;
        }
    }


    ob_start();
    $dtEmissaoRg = isset($dtEmissaoRg) ? $dtEmissaoRg : "";
    geraCampoData("dtEmissaoRg", $dtEmissaoRg, false, "onKeyUp=\"mascaraData(this, event);return autoTab(this, 10, event);\" onBlur=\"JavaScript: if ( !verificaData(this) ) {alertaAviso('@Data inválida!('+this.value+')','form','erro','Sessao::getId()'); frm.dtEmissaoRg.value= ''; };\"" );
    $stDataEmissao = ob_get_clean();
    $catHabilitacao = isset($catHabilitacao) ? $catHabilitacao : "" ;
    $stCategoria = montaComboGenerico("catHabilitacao", "sw_categoria_habilitacao", "cod_categoria", "nom_categoria", $catHabilitacao, "style='width: 200px;' ","", false, true, false,"" ,"Selecione uma categoria");
    ob_start();
    $dtValidadeCnh = isset($dtValidadeCnh) ? $dtValidadeCnh : "";
    geraCampoData( "dtValidadeCnh", $dtValidadeCnh, false, "onKeyUp=\"mascaraData(this, event);return autoTab(this, 10, event);\" onBlur=\"JavaScript: if ( !verificaData(this) ) {alertaAviso('@Data inválida!('+this.value+')','form','erro','Sessao::getId()'); frm.dtValidadeCnh.value= ''; };\"" );
     $stValidadeCNH = ob_get_clean();
     //COMBO NACIONALIDADE
     $sSQL = "SELECT * FROM sw_pais WHERE cod_pais > 0 ORDER by nacionalidade";
     $dbEmp = new dataBaseLegado;
     $dbEmp->abreBD();
     $dbEmp->abreSelecao($sSQL);
     $dbEmp->vaiPrimeiro();
     $comboEstado = "";
     $comboPais = isset($comboPais) ? $comboPais : "" ;
     while (!$dbEmp->eof()) {
         $codg_pais           = trim($dbEmp->pegaCampo("cod_pais"));
         $nomg_nacionalidade  = trim($dbEmp->pegaCampo("nacionalidade"));
         $dbEmp->vaiProximo();
     if ( isset($dadosCgm['nacionalidade']) && ($dadosCgm['nacionalidade'] == $codg_pais) ) {
             $comboPais .= "  <option value=".$codg_pais." selected>".$nomg_nacionalidade."</option>\n";
         } else {
             if ($nomg_nacionalidade=="Brasileira" and !isset($dadosCgm['nacionalidade']) ) {
                 $comboPais .= "  <option value=".$codg_pais." selected>".$nomg_nacionalidade."</option>\n";
             } else {
                 $comboPais .= "<option value=".$codg_pais.">".$nomg_nacionalidade."</option>\n";
             }
         }
     }
     $dbEmp->limpaSelecao();
     $dbEmp->fechaBD();

     //COMBO ESCOLARIDADE
     $sSQL = "SELECT *  FROM sw_escolaridade  ORDER BY cod_escolaridade";
     $dbEmp = new dataBaseLegado;
     $dbEmp->abreBD();
     $dbEmp->abreSelecao($sSQL);
     $dbEmp->vaiPrimeiro();
     $comboEstado = "";
     $comboEscolaridade = isset($comboEscolaridade) ? $comboEscolaridade : "";
     while (!$dbEmp->eof()) {
         $cod_escolaridade = trim($dbEmp->pegaCampo("cod_escolaridade"));
         $stDescricao      = trim($dbEmp->pegaCampo("descricao"));
         $dbEmp->vaiProximo();

         if ( isset($dadosCgm['cod_escolaridade']) && ($dadosCgm['cod_escolaridade'] == $cod_escolaridade) ) {
             $comboEscolaridade .= "  <option value=".$cod_escolaridade ." selected>". $stDescricao ."</option>\n";
         } else {
             $comboEscolaridade .= "<option value=".$cod_escolaridade .">" . $stDescricao ."</option>\n";
         }
     }
     $dbEmp->limpaSelecao();
     $dbEmp->fechaBD();

     //data nascimento
     ob_start();
     $dtNascimento = isset($dtNascimento) ? $dtNascimento : "";
     geraCampoData("dtNascimento", $dtNascimento, false, "onKeyUp=\"mascaraData(this, event);return autoTab(this, 10, event);\" onBlur=\"JavaScript: if ( !verificaData(this) ) {alertaAviso('@Data inválida!('+this.value+')','form','erro','Sessao::getId()'); frm.dtNascimento.value= '';};\"" );
     $stDataNascimento = ob_get_clean();

     //SEXO
     $stMasculino = "checked";
     $stFeminino = "";
     if ( isset($chSexo) && ($chSexo == 'f') ) {
         $stMasculino = "";
         $stFeminino  = "checked";
     }
    $stCheckTipoAlteracaoCorrecao = "checked";
    $stCheckTipoAlteracaoAlteracao = "";
    if ( isset($stTipoAlteracao) && ($stTipoAlteracao == "alteracao") ) {
        $stCheckTipoAlteracaoCorrecao = "";
    $stCheckTipoAlteracaoAlteracao = "checked";
    }

    $nomCgm = isset($nomCgm) ? $nomCgm : "";
    $stHTML = isset($stHTML) ? $stHTML : "";
    $chObrigatorio = isset($chObrigatorio) ? $chObrigatorio : "";
    $cpf = isset($cpf) ? $cpf : "";
    $rg  = isset($rg) ? $rg : "";
    $site  = isset($site) ? $site : "";

if ( !empty($numCgm) ) {
$stHTML = <<<HEREDOC
                Correção <input type='radio' name='stTipoAlteracao' value='correcao' $stCheckTipoAlteracaoCorrecao>
                Alteração <input type='radio' name='stTipoAlteracao' value='alteracao' onChange='if (this.checked == true) { document.frm.nomCgm.value = "";}document.frm.nomCgm.focus();' $stCheckTipoAlteracaoAlteracao>
HEREDOC;
}
$stHTML = <<<HEREDOC
        <tr>
            <td class="label" width="30%" title="Nome do CGM">*Nome</td>
            <td class="field" width="70%">
                <input type="text" name="nomCgm" maxlength="200" size="50" value="$nomCgm" onKeyUp="return autoTab(this, 200, event);">
                $stHTML
            </td>
        </tr>

        <tr>
            <td class="label" title="Informe o CPF.">{$chObrigatorio}CPF</td>
            <td class="field"><input type="text" maxlength="14" name="cpf" size="15" value="$cpf"
            onKeyUp="JavaScript: mascaraCPF(this, event);return autoTab(this, 14, event);"
            onKeyPress="return(isValido(this, event, '0123456789'));"
            onBlur = "JavaScript: if ( !testaCPF( this ) ) {alertaAviso('@CPF inválido!('+this.value+')','form','erro','Sessao::getId()');} "
            onChange = "JavaScript: buscaValor( 600 );" >
            </td>
        </tr>
        <tr>
            <td class="label" title="Documento de identidade">{$chObrigatorio}RG</td>
            <td class="field"><input type="text" name="rg" maxlength="15" size="16" value="$rg"
            onKeyUp="return autoTab(this, 15, event);" ></td>
        </tr>

HEREDOC;
      if ( !isset($inCodUFOrgaoEmissor) ) {
          $inCodigoUFSistema = pegaConfiguracao("cod_uf");
      } else {
          $inCodigoUFSistema = $inCodUFOrgaoEmissor;
      }

$stHTML .= <<<HEREDOC

        <tr>
            <td class="label" width="30%" title="Órgão Emissor do documento de identidade">{$chObrigatorio}Órgão Emissor</td>
            <td class="field" width="70%">
                <input type="text" id="orgaoEmissor" name="orgaoEmissor" maxlength="10" size="12" value="$orgaoEmissor" onKeyUp="return autoTab(this, 10, event);">
            </td>
        </tr>
        <tr>
            <td class="label" title="Selecione o UF do Órgão Emissor do documento de identidade">{$chObrigatorio}UF Órgão Emissor</td>
            <td class="field"><input type="text" id="ufOrgaoEmissor" name="ufOrgaoEmissor"
                                maxlength="20"
                                size="10"
                                value="$inCodigoUFSistema"
                                onchange="document.getElementById('inCodUFOrgaoEmissor').value = this.value"
                              >
                              <select
                                name='inCodUFOrgaoEmissor'
                                id='inCodUFOrgaoEmissor'
                                style='width:150px'
                                onchange="document.getElementById('ufOrgaoEmissor').value = this.value">
                                  <option value=''>Selecione um estado</option>
HEREDOC;
      if ( !isset($inCodUFOrgaoEmissor) ) {
          $inCodigoUFSistema = pegaConfiguracao("cod_uf");
      } else {
          $inCodigoUFSistema = $inCodUFOrgaoEmissor;
      }
      $sSQL = "SELECT * FROM sw_uf WHERE cod_pais <= 1 ORDER by nom_uf";
      $dbEmp = new dataBaseLegado;
      $dbEmp->abreBD();
      $dbEmp->abreSelecao($sSQL);
      $dbEmp->vaiPrimeiro();
      while (!$dbEmp->eof()) {
          $inCodigoUF = trim($dbEmp->pegaCampo("cod_uf"));
          $stNomeUF   = trim($dbEmp->pegaCampo("nom_uf"));
          $dbEmp->vaiProximo();
          if ($inCodigoUFSistema == $inCodigoUF) {
              $stSelected = " selected";
          } else {
              $stSelected = "";
          }
          $stHTML .= "                <option value='".$inCodigoUF."'".$stSelected.">".$stNomeUF."</option>\n";
      }
      $dbEmp->limpaSelecao();
      $dbEmp->fechaBD();

      $numCnh = isset($numCnh) ? $numCnh : "";
      $stPisPasep = isset($stPisPasep) ? $stPisPasep : "";

$stHTML .= <<<HEREDOC
                              </select>
             </td>
        </tr>
        <tr>
            <td class="label" title="Data de emissão do documento de identidade">Data da emissão</td>
            <td class="field">
                 $stDataEmissao
            </td>
        </tr>
        <tr>
            <td class="label" title="Número da Carteira Nacional de Habilitação">Número CNH</td>
            <td class="field"><input type="text" name="numCnh" value="$numCnh" size="15" maxlength="15" onKeyPress="return(isValido(this, event, '0123456789'));"></td>
        </tr>
        <tr>
            <td class="label" title="Categoria da Carteira Nacional de Habilitação">Categoria de habilitação</td>
            <td class="field">
                 $stCategoria
            </td>
        </tr>
        <tr>
            <td class="label" title="Data de validade da Carteira Nacional de Habilitação">Data de validade da CNH</td>
            <td class="field">
                 $stValidadeCNH
            </td>
        </tr>
        <tr>

        <tr>
            <td class="label" title="Informe o número do PIS/PASEP.">PIS/PASEP</td>
            <td class="field"><input type="text" name="stPisPasep" id="stPisPasep" value="$stPisPasep" size="15" maxlength="14" onBlur="javascript:ValidaPisPasep(this.value);" onKeyPress="return(isValido(this, event, '0123456789'));" onkeyup="JavaScript:mascaraDinamico('999.99999.99-9', this, event);"></td>
        </tr>


            <td class="label" title="País de Origem">*Nacionalidade</td>
            <td class="field">
                <select name="nacionalidade" style="width: 300px">
                    <option value="xxx">Selecione uma nacionalidade</option>
                    $comboPais
                </select>
            </td>
        </tr>

        <tr>
            <td class="label" title="Escolaridade">{$chObrigatorio}Escolaridade</td>
            <td class="field">
                <select name="cod_escolaridade" style="width: 300px">
                   $comboEscolaridade
                </select>
            </td>
        </tr>
        <tr>
            <td class="label" title="Data de nascimento">Data de nascimento</td>
            <td class="field">
                   $stDataNascimento
            </td>
        </tr>
        <tr>
            <td class="label">*Sexo</td>
            <td class="field">
                <input type='radio' value='m' name='chSexo' $stMasculino> Masculino&nbsp;
                <input type='radio' value='f' name='chSexo' $stFeminino> Feminino&nbsp;
             </td>
        </tr>

HEREDOC;

return $stHTML;
}

/***************************************************************************
Monta o formulário com os dados para cadastro de CGM
Se a variável $dados Cgm for maior que zero ele carrega também os dados do CGM
/**************************************************************************/
    public function formCgm($dadosCgm="",$action="",$controle=0)
    {
        $stMunicipio = "";
        $stUF = "";

    if (is_array($dadosCgm)) {
            //Grava como variável o nome da chave do vetor com o seu respectivo valor
            foreach ($dadosCgm as $campo=>$valor) {
        if (is_array($valor)) {
            foreach ($valor as $campo=>$valores) {
            $$campo = trim($valores);
            }
        } else {
            $$campo = trim($valor);
        }
            }

            //Carrega o cep em partes para preencher os campos segmentados
            if (isset($cep)) {
                if ($cep) {
                    $cep = preg_replace("/[^0-9a-zA-Z]/","", $cep);
                    $cep1 = substr($cep,0,5);
                    $cep2 = substr($cep,5,3);
                    $cep = $cep1."-".$cep2;
                }
            }
            if (isset($cepCorresp)) {
                if ($cepCorresp) {
                    $cepCorresp  = preg_replace("/[^0-9a-zA-Z]/","", $cepCorresp);
                    $cepCorresp1 = substr($cepCorresp,0,5);
                    $cepCorresp2 = substr($cepCorresp,5,3);
                    $cepCorresp  = $cepCorresp1."-".$cepCorresp2;
                }
            }

            //Carrega os telefones em partes para preencher os campos segmentados
            if (!isset($dddRes)) {
                $foneRes = isset($foneRes) ? $foneRes : "";
                $dddRes = substr($foneRes,0,2);
                $foneRes = substr($foneRes,2,8);
            }
            if (!isset($dddCom)) {
                $foneCom = isset($foneCom) ? $foneCom : "";
                $dddCom = substr($foneCom,0,2);
                $foneCom = substr($foneCom,2,8);
            }
            if (!isset($dddCel)) {
                $foneCel = isset($foneCel) ? $foneCel : "";
                $dddCel = substr($foneCel,0,2);
                $foneCel = substr($foneCel,2,8);
            }


        if ( isset($dadosCgm["codMunicipio"]) ) {
                $stMunicipio = $dadosCgm["codMunicipio"]." - ".$dadosCgm["nomMunicipio"];
            }

            if ( isset($dadosCgm["codUf"]) ) {
                $stUF = $dadosCgm["codUf"]." - ".$dadosCgm["nomUf"];
            }

            if ( isset($dadosCgm["codMunicipioCorresp"]) && isset($dadosCgm["cod_logradouro_corresp"])) {
                $stMunicipioCorresp = $dadosCgm["codMunicipioCorresp"]." - ".$dadosCgm["municipioCorresp"];
            }

            if ( isset($dadosCgm["codUfCorresp"]) && isset($dadosCgm["cod_logradouro_corresp"])) {
                $stUFCorresp = $dadosCgm["codUfCorresp"]." - ".$dadosCgm["estadoCorresp"];
            }
        }
?>
        <script type="text/javascript">
            function habilitaEnderecoCorrespondencia()
            {
                if ( document.getElementById('endCorresp').style.display=="none" ) {
                     document.getElementById('endCorresp').style.display="block";
                     document.getElementById('imgBotao').src='<?=CAM_FW_IMAGENS.'botao_retrair15px.png';?>';
                } else {
                     document.getElementById('endCorresp').style.display="none"
                     document.getElementById('imgBotao').src='<?=CAM_FW_IMAGENS.'botao_expandir15px.png';?>';
                }
            }

            function validaLetraRG(letra)
            {
                if (((letra>57 && letra<65) || (letra>90 && letra<97) || (letra>122 && letra<192) || (letra>196 && letra<199)                || (letra>207 && letra<210) || (letra>214 && letra<217) || (letra>221 && letra<224) || (letra>228 && letra<231) || (letra>246 && letra<249) || letra>253 || letra<45) && letra!=150) {
                    return true;
                } else {
                    return false;
                }
            }

            function validaLetraNome(letra)
            {
                 if ((letra>90 && letra<97) || (letra>122 && letra<192) || (letra>196 && letra<199) || (letra>207 && letra<210) || (letra>214 && letra<217) || (letra>221 && letra<224) || (letra>228 && letra<231) || (letra>246 && letra<249) || letra>253 || letra<65) {
                     return true;
                 } else {
                     return false;
                 }
             }

             function validaNome(nome)
             {
                 var mensagem = '';
                 if (nome.substr(1,1)==' ' || nome.substr(1,1)=='') {
                     mensagem = 'Primeiro nome deve conter no mínimo duas letras!';
                 } else {
                     palavras = nome.split(' ');
                     erroNome = false;
                     for (i = 0 ; i < palavras.length ; i++) {
                         if (validaLetraNome(palavras[i].charCodeAt(palavras[i].length-1)) && palavras[i].charCodeAt(palavras[i].length-1)!=46) {
                             erroNome = palavras[i];
                             break;
                         }
                     }
                     if (erroNome) {
                         mensagem = 'Caracter inválido no fim do nome!';
                         mensagem = '';
                     } else {
//                       if ((palavras[i-1].charCodeAt(palavras[i-1].length-1)<97 && palavras[i-1].charCodeAt(palavras[i-1].length-1)>90) || palavras[i-1].charCodeAt(palavras[i-1].length-1)>123 || palavras[i-1].charCodeAt(palavras[i-1].length-1)<65) {
                         if (validaLetraNome(palavras[i-1].charCodeAt(palavras[i-1].length-1))) {
                             mensagem = 'Último nome não pode ser abreviado!';
                         }
                     }
                 }

                 return mensagem;
             }



            function Valida()
            {
                var mensagem = "";
                var erro = false;
                var campo;
                var campoaux;
                var data1;
                var data2;
                var data3;
                var erroRG = false;


    <?php if ($pessoa == 'juridica') { ?>
                campo = document.frm.nomCgm.value.length;
                if (campo==0) {
                    mensagem += "@Campo Razão Social inválido!()";
                    erro = true;
                }

                campo = document.frm.cnpj.value.length;
                if (campo<18) { // Campo cnpj tem que ter 14 caracteres >
                    mensagem += "@Campo CNPJ inválido!("+document.frm.cnpj.value+")";
                    erro = true;
                }

                campoaux = document.frm.cnpj.value;
                var expReg = new RegExp("[^a-zA-Z0-9]","g");
                var campoAuxDesmasc = campoaux.replace(expReg, '');
                if (campo==18) {
                    if (!VerificaCNPJ(campoAuxDesmasc)) { //> Verifica se o CNPJ é válido
                        mensagem += "@Campo CNPJ inválido!("+campoaux+")";
                        erro = true;
                    }
                    stCampo = document.frm.inscEstadual;
                    if ( !isInt( stCampo.value ) ) {
                         erro = true;
                         mensagem += "@Campo Inscrição Estadual Inválido!("+stCampo.value+")";
                    }
                }

    <?php } elseif ($pessoa == 'fisica') { ?>
                campo = document.frm.nomCgm.value.length;
                if (campo==0) {
                    mensagem += "@Campo Nome inválido!()";
                    erro = true;
                } else {
                     campo = document.frm.nomCgm.value;
                     var mensagemTmp = validaNome( campo );
                     if (mensagemTmp.length > 0) {
                         mensagem += "@Campo Nome inválido!("+mensagemTmp+")";
                         erro = true;
                     }
                }

                campo = document.frm.cpf.value.length;
                if (campo<14) { //> Campo cpf tem que ter 11 caracteres
                    mensagem += "@Campo CPF inválido!("+document.frm.cpf.value+")";
                    erro = true;
                }

                campoaux = document.frm.cpf.value;
                var expReg = new RegExp("[^a-zA-Z0-9]","g");
                var campoAuxDesmasc = campoaux.replace(expReg, '');
                if (campo==14) {
                   if (!VerificaCPF(campoAuxDesmasc)) { //> Verifica se o CPF é válido
                        mensagem += "@Campo CPF inválido!("+campoaux+")";
                        erro = true;
                     }
                }

                campo = document.frm.rg.value.length;
                if (campo==0) {
                    mensagem += "@Campo RG inválido!()";
                    erro = true;
                } else {
                    rg = document.frm.rg.value.split('');
                    for (i = 0 ; i < rg.length ; i++) {
                        if (validaLetraRG(rg[i].charCodeAt(0))) {
                            erroRG = rg[i];
                            break;
                        }
                    }
                    if (erroRG) {
                        mensagem += '@Caracter inválido no Campo RG!';
                        erro = true;
                    }
                }

                campo = document.frm.orgaoEmissor.value.length;
                if (campo==0) {
                    mensagem += "@Campo Órgão Emissor inválido!()";
                    erro = true;
                }
                
                campo = document.frm.ufOrgaoEmissor.value.length;
                if (campo==0) {
                    mensagem += "@Campo UF Órgão Emissor inválido!()";
                    erro = true;
                }

                campo = document.frm.cod_escolaridade.value;
                if (campo=='xxx') {
                    mensagem += "@Campo Escolaridade inválido!()";
                    erro = true;
                }

                if ( !verificaData( document.frm.dtNascimento ) ) {
                    mensagem += "@Campo Data de Nascimento inválido!("+campo+")";
                    erro = true;
                }

                campo = document.frm.nacionalidade.value;
                if (campo=='xxx') {
                    mensagem += "@Campo Nacionalidade inválido!()";
                    erro = true;
                }

                campo = document.frm.numCnh.value;
                if (campo.length > 0) {
                    expReg = new RegExp("[^a-zA-Z0-9]","ig");
                    if ( expReg.test( campo ) ) {
                        mensagem += "@Campo CNH inválido!("+campo+")";
                        erro = true;
                    }
                }

                var dataCNH = document.frm.dtValidadeCnh;
                if (dataCNH.value.length > 0) {
                    if ( !verificaData(dataCNH) ) {
                        mensagem += "@Campo Data de validade da CNH inválido!("+dataCNH.value+")";
                        erro = true;
                    }
                }
    <?php } elseif ($pessoa == 'outros') {
               if ($tipo == 'fisica') {
?>
                campo = document.frm.nomCgm.value.length;
                if (campo==0) {
                    mensagem += "@Campo Nome inválido!()";
                    erro = true;
                } else {
                     campo = document.frm.nomCgm.value;
                     var mensagemTmp = validaNome( campo );
                     if (mensagemTmp.length > 0) {
                         mensagem += "@Campo Nome inválido!("+mensagemTmp+")";
                         erro = true;
                     }
                }

                campo = document.frm.cpf.value.length;
                if (campo>0 && campo<14) { //> Campo cpf tem que ter 11 caracteres
                    mensagem += "@Campo CPF inválido!("+document.frm.cpf.value+")";
                    erro = true;
                }

                campoaux = document.frm.cpf.value;
                var expReg = new RegExp("[^a-zA-Z0-9]","g");
                var campoAuxDesmasc = campoaux.replace(expReg, '');
                if (campo==14) {
                   if (!VerificaCPF(campoAuxDesmasc)) { //> Verifica se o CPF é válido
                        mensagem += "@Campo CPF inválido!("+campoaux+")";
                        erro = true;
                     }
                }

                campo = document.frm.rg.value.length;
                if (campo>0) {
                    rg = document.frm.rg.value.split('');
                    for (i = 0 ; i < rg.length ; i++) {
                        if (validaLetraRG(rg[i].charCodeAt(0))) {
                            erroRG = rg[i];
                            break;
                        }
                    }
                    if (erroRG) {
                        mensagem += '@Caracter inválido no Campo RG!';
                        erro = true;
                    }
                }

                campo = document.frm.numCnh.value;
                if (campo.length > 0) {
                    expReg = new RegExp("[^a-zA-Z0-9]","ig");
                    if ( expReg.test( campo ) ) {
                        mensagem += "@Campo CNH inválido!("+campo+")";
                        erro = true;
                    }
                }

                if ( !verificaData( document.frm.dtNascimento ) ) {
                    mensagem += "@Campo Data de Nascimento inválido!("+campo+")";
                    erro = true;
                }

                campo = document.frm.nacionalidade.value;
                if (campo=='xxx') {
                    mensagem += "@Campo Nacionalidade inválido!()";
                    erro = true;
                }

    <?php
               } else {
    ?>
                campo = document.frm.nomCgm.value.length;
                if (campo==0) {
                    mensagem += "@Campo Razão Social inválido!()";
                    erro = true;
                }

                campo = document.frm.cnpj.value.length;
                if (campo>0 && campo<18) { // Campo cnpj tem que ter 14 caracteres >
                    mensagem += "@Campo CNPJ inválido!("+document.frm.cnpj.value+")";
                    erro = true;
                }

                campoaux = document.frm.cnpj.value;
                var expReg = new RegExp("[^a-zA-Z0-9]","g");
                var campoAuxDesmasc = campoaux.replace(expReg, '');
                if (campo==18) {
                    if (!VerificaCNPJ(campoAuxDesmasc)) { //> Verifica se o CNPJ é válido
                        mensagem += "@Campo CNPJ inválido!("+campoaux+")";
                        erro = true;
                    }
                    stCampo = document.frm.inscEstadual;
                    if ( !isInt( stCampo.value ) ) {
                         erro = true;
                         mensagem += "@Campo Inscrição Estadual Inválido!("+stCampo.value+")";
                    }
                }



    <?php
               }
        }
    ?>

    <?php
                if ($dadosCgm["apresentar_alteracao"] && $dadosCgm["logradouroCorresp"]) {
    ?>
                campo = document.frm.inNumLogradouroCorresp.value.length;
                if (campo == 0) {
                    mensagem += "@Campo Logradouro Correspondência inválido!()";
                    erro = true;
                }

                campo = document.frm.inNumeroCorresp.value.length;
                if (campo==0) {
                    mensagem += "@Campo Número Correspondência inválido!()";
                    erro = true;
                }

                campo = document.frm.inCodigoBairroCorresp.value.length;
                if (campo==0) {
                    mensagem += "@Campo Bairro Correspondência inválido!()";
                    erro = true;
                }

                campo = document.frm.cmbCEPCorresp.value.length;
                if (campo==0) {
                    mensagem += "@Campo CEP Correspondência inválido!()";
                    erro = true;
                }

    <?php
                }
    ?>

                campo = document.frm.inNumLogradouro.value.length;
                if (campo==0) {
                    mensagem += "@Campo Logradouro inválido!()";
                    erro = true;
                }

                campo = document.frm.inNumero.value.length;
                if (campo==0) {
                    mensagem += "@Campo Número inválido!()";
                    erro = true;
                }

                campo = document.frm.pais.value;
                if (campo=='xxx') {
                    mensagem += "@Campo País inválido!()";
                    erro = true;
                }

                campo = document.frm.inCodigoBairro.value.length;
                if (campo==0) {
                    mensagem += "@O campo Bairro inválido!()";
                    erro = true;
                }

                campo = document.frm.cmbCEP.value.length;
                if (campo==0) {
                    mensagem += "@Campo CEP inválido!()";
                    erro = true;
                }

                campo = document.frm.inNumLogradouroCorresp.value.length;
                if (campo!=0) {
                    campoNumero = document.frm.inNumeroCorresp.value.length;
                    if (campoNumero==0) {
                        mensagem += "@Como foi informado o logradouro informar também o número para correspondência!()";
                        erro = true;
                    }
                    campoBairro = document.frm.inCodigoBairroCorresp.value.length;
                    if (campoBairro==0) {
                        mensagem += "@Campo Bairro Correspondência inválido!()";
                        erro = true;
                    }
                    campoCEP = document.frm.cmbCEPCorresp.value.length;
                    if (campoCEP==0) {
                        mensagem += "@Campo CEP Correspondência inválido!()";
                        erro = true;
                    }
                }

            <?php if ($pessoa == 'fisica' or ( $pessoa == 'outros' and $tipo == 'fisica' )) { ?>
                if (document.frm.dddRes.value != '' && document.frm.foneRes.value == '') {
                    mensagem += "@@Campo DDD do Telefone residencial foi preenchido, por favor preencher o campo correspondente ao Número do Telefone residencial!";
                    erro = true;
                }
                
                if (document.frm.dddRes.value == '' && document.frm.foneRes.value != '') {
                    mensagem += "@Campo Número do Telefone residencial foi preenchido, por favor preencher o campo correspondente ao DDD do Telefone residencial!";
                    erro = true;
                }

                if (document.frm.dddCom.value != '' && document.frm.foneCom.value == '') {
                    mensagem += "@@Campo DDD do Telefone comercial foi preenchido, por favor preencher o campo correspondente ao Número do Telefone comercial!";
                    erro = true;
                }
                
                if (document.frm.dddCom.value == '' && document.frm.foneCom.value != '') {
                    mensagem += "@Campo Número do Telefone comercial foi preenchido, por favor preencher o campo correspondente ao DDD do Telefone comercial!";
                    erro = true;
                }
            <?php } else { ?>
                if (document.frm.dddRes.value != '' && document.frm.foneRes.value == '') {
                    mensagem += "@@Campo DDD do Telefone foi preenchido, por favor preencher o campo correspondente ao Número do Telefone!";
                    erro = true;
                }
                
                if (document.frm.dddRes.value == '' && document.frm.foneRes.value != '') {
                    mensagem += "@Campo Número do Telefone foi preenchido, por favor preencher o campo correspondente ao DDD do Telefone!";
                    erro = true;
                }

                if (document.frm.dddCom.value != '' && document.frm.foneCom.value == '') {
                    mensagem += "@@Campo DDD do FAX foi preenchido, por favor preencher o campo correspondente ao Número do FAX!";
                    erro = true;
                }
                
                if (document.frm.dddCom.value == '' && document.frm.foneCom.value != '') {
                    mensagem += "@Campo Número do FAX foi preenchido, por favor preencher o campo correspondente ao DDD do FAX!";
                    erro = true;
                }
            <?php } ?>

                if (document.frm.dddCel.value != '' && document.frm.foneCel.value == '') {
                    mensagem += "@@Campo DDD do Telefone celular foi preenchido, por favor preencher o campo correspondente ao Número do Telefone celular!";
                    erro = true;
                }
                
                if (document.frm.dddCel.value == '' && document.frm.foneCel.value != '') {
                    mensagem += "@Campo Número do Telefone celular foi preenchido, por favor preencher o campo correspondente ao DDD do Telefone celular!";
                    erro = true;
                }
                
                campo = document.frm.email.value.length;
                if (campo>0) {
                    if (!(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(document.frm.email.value))) {
                        mensagem += "@Campo e-mail inválido!("+document.frm.email.value+")";
                        erro = true;
                    }
                }

                campo = document.frm.emailAdic.value.length;
                if (campo>0) {
                    if (!(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(document.frm.emailAdic.value))) {
                        mensagem += "@Campo e-mail inválido!("+document.frm.emailAdic.value+")";
                        erro = true;
                    }
                }

                campo = document.frm.dddRes.value;
                campoaux = document.frm.foneRes.value;
                if (isNaN(campo || isNaN(campoaux))) {
                    mensagem += "@Campo Telefone Residencial inválido!("+campo+" "+campoaux+")";
                    erro = true;
                }

                campo = document.frm.dddCom.value;
                campoaux = document.frm.foneCom.value;
                if (isNaN(campo || isNaN(campoaux))) {
                    mensagem += "@Campo Telefone Comercial inválido!("+campo+" "+campoaux+")";
                    erro = true;
                }

                campo = document.frm.dddCel.value;
                campoaux = document.frm.foneCel.value;
                if (isNaN(campo || isNaN(campoaux))) {
                    mensagem += "@Campo Telefone Celular inválido!("+campo+" "+campoaux+")";
                    erro = true;
                }

                if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
                return !(erro);
            }// Fim da function Valida

            // Retirada a validaçao de pessoa fisica, pois pode ser interno sem cpf
            // neste caso ocorreria erro da chamada da funcao
            function testaCPF(campoCpf)
            {
                var erro = true;
                var expReg = new RegExp("[^a-zA-Z0-9]","g");
                var campoCpfDesmasc = campoCpf.value.replace(expReg, '');
                if (campoCpfDesmasc.length > 0) {
                    if (campoCpf.value.length == 14) {
                        if ( !VerificaCPF( campoCpfDesmasc ) ) {//> Verifica se o CPF é válido
                            erro = false;
                        }
                    } else {
                        erro = false;
              }
          }

          return erro;
      }

            //A função salvar testa a validação, e se tudo ocorrer certo, envia o form
            function Salvar()
            {
                document.frm.ok.disabled = true;
                if (Valida()) {
                    document.frm.controle.value = "<?=$controle+1;?>";
                    document.frm.submit();
                    document.frm.ok.disabled = false;
                } else {
                    document.frm.ok.disabled = false;
                }
            }

            function Limpar()
            {
                document.frm.reset();
                document.getElementById("campoInnerLogr").innerHTML = "&nbsp;";
                document.getElementById("stMunicipio").innerHTML = "&nbsp;";
                document.getElementById("stEstado").innerHTML = "&nbsp;";
                document.getElementById("campoInnerLogrCorresp").innerHTML = "&nbsp;";
                document.getElementById("stMunicipioCorresp").innerHTML = "&nbsp;";
                document.getElementById("stEstadoCorresp").innerHTML = "&nbsp;";
            }

            function CopiaDoTextoProComboBairro()
            {
                document.frm.cmbBairro.value = document.frm.inCodigoBairro.value;
            }

            function CopiaDoComboProTexto()
            {
                document.frm.inCodigoBairro.value = document.frm.cmbBairro.value;
            }

            function ValidaPisPasep(stPisPasep)
            {
                     stPisPasep = stPisPasep.replace('.', '').replace('.', '').replace('-', '');

                     if(stPisPasep.length != 11)
                        boExecuta = true;

                    if( isNaN(stPisPasep) )
                        boExecuta = true;

                    if(stPisPasep == '00000000000')
                        boExecuta = true;

                     var intResultado = new String();
                     var intTotal = 0;
                     var strPeso = '3298765432';

                     for (i = 1; i <= 10; i++) {
                        intResultado = stPisPasep.substring(i, i - 1) * strPeso.substring(i, i - 1);
                        intTotal += intResultado;
                     }

                     var intResto = intTotal % 11;
                     if(intResto != 0)
                        intResto = 11 - intResto;
                     if(intResto == 10 || intResto == 11)
                        intResto = intResto.toString().substring(2, 1);
                     if(parseInt(intResto, 10) != parseInt(stPisPasep.substring(11, 10), 10))
                        boExecuta = true;

                     if (boExecuta == true) {
                        boExecuta = false;
                        document.frm.stPisPasep.value = '';
                        alertaAviso('Número PIS/PASEP inválido!','form','erro','<?=Sessao::getId();?>');
                     }
            }

            function CopiaDoTextoProComboBairroCorresp()
            {
                document.frm.cmbBairroCorresp.value = document.frm.inCodigoBairroCorresp.value;
            }

            function CopiaDoComboProTextoCorresp()
            {
                document.frm.inCodigoBairroCorresp.value = document.frm.cmbBairroCorresp.value;
            }


            function abrePopUpCgm(arquivo,nomeform,camponum,camponom,tipodebusca,sessao,width,height)
            {
                if (width == '') {
                    width = 800;
                }
                if (height == '') {
                    height = 550;
                }
                var x = 0;
                var y = 0;
                var sessaoid = sessao.substr(10,6);
                var sArq = ''+arquivo+'?'+sessao+'&nomForm='+nomeform+'&campoNum='+camponum+'&campoNom='+camponom+'&tipoBusca='+tipodebusca+'&inCodPais='+document.frm.pais.value;
                var sAux = "window.open(sArq,'','width="+width+",height="+height+",resizable=1,scrollbars=1,left="+x+",top="+y+"');";
                eval(sAux);
            }

            function abrePopUpCgmCorresp(arquivo,nomeform,camponum,camponom,tipodebusca,sessao,width,height)
            {
                if (width == '') {
                    width = 800;
                }
                if (height == '') {
                    height = 550;
                }
                var x = 0;
                var y = 0;
                var sessaoid = sessao.substr(10,6);
                var sArq = ''+arquivo+'?'+sessao+'&nomForm='+nomeform+'&campoNum='+camponum+'&campoNom='+camponom+'&tipoBusca='+tipodebusca+'&inCodPais='+document.frm.paisCorresp.value;
                var sAux = "window.open(sArq,'','width="+width+",height="+height+",resizable=1,scrollbars=1,left="+x+",top="+y+"');";
                eval(sAux);
            }


            function buscaValor(controle)
            {
                var stTraget = document.frm.target;

                document.frm.target = "oculto";
                document.frm.controle.value = controle;
                document.frm.submit();
                document.frm.target = stTraget;
            }

            function atualizadados(campo, controle)
            {
                var targetTmp = document.frm.target;
                document.frm.target = "oculto";
                var actionTmp = document.frm.action;
                document.frm.campoMunicipio.value = campo;
                document.frm.controle.value = controle;
                document.frm.submit();
                document.frm.target = targetTmp;
            }

            function Cancela()
            {
                document.frm.action += "&pagina=<?=$_GET['pagina'];?>&volta=true";
                document.frm.controle.value = 2;
                document.frm.submit();
            }

        </script>

    <?php
        $controle = isset($controle) ? $controle : "";
        $numCgm = isset($numCgm) ? $numCgm : "";
        $pessoa = isset($pessoa) ? $pessoa : "";
        $tipo   = isset($tipo) ? $tipo : "";
        $codUf  = isset($codUf) ? $codUf : "";
        $codMunicipio = isset($codMunicipio) ? $codMunicipio : "";
        $codUfCorresp = isset($codUfCorresp) ? $codUfCorresp : "";
        $codMunicipioCorresp = isset($codMunicipioCorresp) ? $codMunicipioCorresp : "";
    ?>

    <form name='frm' method='post' action='<?=$action;?>?<?=Sessao::getId();?>'>
    <table width="100%">
        <tr>
            <td class=alt_dados colspan=2>
                Dados para CGM
                <input type="hidden" name="controle" value='<?=$controle;?>'>
                <input type="hidden" name="numCgm" value='<?=$numCgm;?>'>
                <input type="hidden" name="pessoa" value='<?=$pessoa;?>'>
                <input type="hidden" name="tipo" value='<?=$tipo;?>'>
                <input type="hidden" name="codUF" value='<?=$codUf;?>'>
                <input type="hidden" name="codMunicipio" value='<?=$codMunicipio;?>'>
                <input type="hidden" name="codUfCorresp" value='<?=$codUfCorresp;?>'>
                <input type="hidden" name="codMunicipioCorresp" value='<?=$codMunicipioCorresp;?>'>
                <input type="hidden" name="campoMunicipio" value=''>
            </td>
        </tr>
<?php
if ($numCgm) {
echo <<<HEREDOC
        <tr>
            <td class="label" width="30%" title="Número do CGM">CGM</td>
            <td class="field" width="70%">$numCgm
            </td>
        </tr>
HEREDOC;
}

if ($pessoa == 'juridica') {
    echo $this->formCGMPessoaJuridica( $dadosCgm );
} elseif ($pessoa == 'fisica') {
    echo $this->formCGMPessoaFisica( $dadosCgm );
} elseif ($pessoa == "outros") {
    if ($_REQUEST["tipo"]<>"" && $tipo=="") {
        $tipo = $_REQUEST["tipo"];
    }
    if ($tipo == 'fisica') {
         echo $this->formCGMPessoaFisica( $dadosCgm, true );
    } else {
         echo $this->formCGMPessoaJuridica( $dadosCgm, true );
    }
}

    if ( isset($dadosCgm["apresentar_alteracao"]) ) {

        $obLblPais = new Label;
        $obLblPais->setName       ('lblPais');
        $obLblPais->setRotulo     ('País');
        $obLblPais->setValue      ( $dadosCgm["pais"] );
        $obLblPais->setId         ('lblPais');

        $obLblEstado = new Label;
        $obLblEstado->setName       ('lblEstado');
        $obLblEstado->setRotulo     ('Estado');
        $obLblEstado->setValue      ( $dadosCgm["nomUf"] );
        $obLblEstado->setId         ('lblEstado');

        $obLblMunicipio = new Label;
        $obLblMunicipio->setName       ('lblCidade');
        $obLblMunicipio->setRotulo     ('Cidade');
        $obLblMunicipio->setValue      ( $dadosCgm["nomMunicipio"] );
        $obLblMunicipio->setId         ('lblCidade');

        $obLblTipoLogradouro = new Label();
        $obLblTipoLogradouro->setRotulo ("Tipo");
        $obLblTipoLogradouro->setName   ("inCodigoTipo");
        $obLblTipoLogradouro->setId     ("inCodigoTipo");
        $obLblTipoLogradouro->setValue  ($dadosCgm['tipoLogradouro']);        

        $obLblLogradouro = new Label;
        $obLblLogradouro->setName       ('lblLogradouro');
        $obLblLogradouro->setRotulo     ('Logradouro');
        $obLblLogradouro->setValue      ( $dadosCgm["logradouro"] );
        $obLblLogradouro->setId         ('lblLogradouro');

        $obLblNumero = new Label;
        $obLblNumero->setName       ('lblNumero');
        $obLblNumero->setRotulo     ('Número');
        $obLblNumero->setValue      ( $dadosCgm["numero"] );
        $obLblNumero->setId         ('lblNumero');

        $obLblComplemento = new Label;
        $obLblComplemento->setName       ('lblComplemento');
        $obLblComplemento->setRotulo     ('Complemento');
        $obLblComplemento->setValue      ( $dadosCgm["complemento"]?$dadosCgm["complemento"]:"&nbsp;" );
        $obLblComplemento->setId         ('lblComplemento');

        $obLblBairro = new Label;
        $obLblBairro->setName       ('lblBairro');
        $obLblBairro->setRotulo     ('Bairro');
        $obLblBairro->setValue      ( $dadosCgm["bairro"]?$dadosCgm["bairro"]:"&nbsp;" );
        $obLblBairro->setId         ('lblBairro');

        $obLblCEP = new Label;
        $obLblCEP->setName       ('lblCep');
        $obLblCEP->setRotulo     ('CEP');
        $obLblCEP->setValue      ( $dadosCgm["cep"]?$dadosCgm["cep"]:"&nbsp;" );
        $obLblCEP->setId         ('lblCep');


        echo "<tr>";
        echo "  <td colspan='2' class='alt_dados'>Dados de endereço atual</td></tr>";
        echo "<tr>";

        $obLblPais->montaHTML();
        $stHTML = $obLblPais->getHtml ();

        echo "<tr>";
        echo "<td class=\"label\" title=\"País.\">País</td>";
        echo "<td class=\"field\">".$stHTML."</td>";
        echo "</tr>";

        $obLblEstado->montaHTML();
        $stHTML = $obLblEstado->getHtml ();

        echo "<tr>";
        echo "<td class=\"label\" title=\"Estado.\">Estado</td>";
        echo "<td class=\"field\">".$stHTML."</td>";
        echo "</tr>";

        $obLblMunicipio->montaHTML();
        $stHTML = $obLblMunicipio->getHtml ();

        echo "<tr>";
        echo "<td class=\"label\" title=\"Cidade.\">Cidade</td>";
        echo "<td class=\"field\">".$stHTML."</td>";
        echo "</tr>";

        $obLblTipoLogradouro->montaHTML();
        $stHTML = $obLblTipoLogradouro->getHtml ();

        echo "<tr>";
        echo "<td class=\"label\" title=\"Tipo.\">Tipo</td>";
        echo "<td class=\"field\">".$stHTML."</td>";
        echo "</tr>";

        $obLblLogradouro->montaHTML();
        $stHTML = $obLblLogradouro->getHtml ();

        echo "<tr>";
        echo "<td class=\"label\" title=\"Logradouro.\">Logradouro</td>";
        echo "<td class=\"field\">".$stHTML."</td>";
        echo "</tr>";

        $obLblNumero->montaHTML();
        $stHTML = $obLblNumero->getHtml ();

        echo "<tr>";
        echo "<td class=\"label\" title=\"Número.\">Número</td>";
        echo "<td class=\"field\">".$stHTML."</td>";
        echo "</tr>";

        $obLblComplemento->montaHTML();
        $stHTML = $obLblComplemento->getHtml ();

        echo "<tr>";
        echo "<td class=\"label\" title=\"Complemento.\">Complemento</td>";
        echo "<td class=\"field\">".$stHTML."</td>";
        echo "</tr>";

        $obLblBairro->montaHTML();
        $stHTML = $obLblBairro->getHtml ();

        echo "<tr>";
        echo "<td class=\"label\" title=\"Bairro.\">Bairro</td>";
        echo "<td class=\"field\">".$stHTML."</td>";
        echo "</tr>";

        $obLblCEP->montaHTML();
        $stHTML = $obLblCEP->getHtml ();

        echo "<tr>";
        echo "<td class=\"label\" title=\"CEP.\">CEP</td>";
        echo "<td class=\"field\">".$stHTML."</td>";
        echo "</tr>";

        // Verifica se o CGM utiliza as tabelas novas para cadastro de logradouro, na alteração de CGM
        $sQl = pegaDado("numcgm", "sw_cgm_logradouro"," where numcgm = ". $dadosCgm['numCgm']);

        if (!$sQl) {
            echo "<tr>";
            echo "      <td colspan='2' class='label'>
                            <Center align='center'>
                                <font color='#CC0000'>
                                    Este CGM não possui o seu endereço atualizado, atualize-o preenchendo os campos abaixo.
                                </font><br>
                                <font color='black' size='+0.5'>
                                    Os endereços do CGM serão agora vinculados aos logradouros cadastrados no Urbem. Para realizar a atualização, preencha os campos abaixo com os 'Dados para atualização de endereço' e confirme a alteração, então este registro não apresentará mais este aviso.
                                </font>
                            </Center>
                        </td>";
            echo "</tr>";
        }

        echo "<tr>";
        echo "    <td colspan='2' class='alt_dados'>Dados para atualização de endereço</td></tr>";
        echo "<tr>";
    } else {
        echo "<tr>";
        echo "    <td colspan='2' class='alt_dados'>Dados de endereço</td></tr>";
        echo "<tr>";
    }
?>

            <td class="label" title="Selecione o país.">*País</td>
            <td class="field">
                <select name="pais" onChange="javascript: atualizadados('estado','2000')" style="width: 300px">
                    <option value="xxx">Selecione um País</option>
<?php
    $sSQL = "select cod_pais, nom_pais from sw_pais where cod_pais != 2 order by nom_pais";
    $dbEmp = new dataBaseLegado;
    $dbEmp->abreBD();
    $dbEmp->abreSelecao($sSQL);
    $dbEmp->vaiPrimeiro();
    $comboPaisEstadoCidade = "";
    $inCodPais = isset($inCodPais) ? $inCodPais : null;
    if (empty($dadosCgm['cod_pais']) and is_null($inCodPais)) {
       $dadosCgm['cod_pais'] = $inCodpais = 1;
    } else {
        if (empty($dadosCgm['cod_pais'])) {
           $inCodpais = $codpais;
        } else {
           $inCodpais = $dadosCgm['cod_pais'];
        }
    }
    $codpais = isset($codpais) ? $codpais : "";
    while (!$dbEmp->eof()) {
        $cod_pais  = trim($dbEmp->pegaCampo("cod_pais"));
        $nom_pais  = trim($dbEmp->pegaCampo("nom_pais"));
        $dbEmp->vaiProximo();
        if ($dadosCgm['cod_pais'] == $cod_pais) {
            $comboPaisEstadoCidade .= " <option value=".$cod_pais." selected>".$nom_pais."</option>\n";
        } else {
            if ($codpais == $cod_pais) {
                $comboPaisEstadoCidade .= " <option value=".$cod_pais." selected>".$nom_pais."</option>\n";
            } else {
                $comboPaisEstadoCidade .= " <option value=".$cod_pais.">".$nom_pais."</option>\n";
            }
        }
    }
    $dbEmp->limpaSelecao();
    $dbEmp->fechaBD();
    echo "$comboPaisEstadoCidade";
?>
                </select>
            </td>
        </tr>


<?php
    include_once ( CAM_GT_CIM_NEGOCIO."RCIMTrecho.class.php" );
    $obRCIMTrecho  = new RCIMTrecho;

    $obTxtNumero = new TextBox;
    $obTxtNumero->setName       ('inNumero');
    $obTxtNumero->setRotulo     ('Número');
    $obTxtNumero->setInteiro    ( false );
    $obTxtNumero->setMaxLength  ( 6 );
    $obTxtNumero->setSize       ( 8 );
    $obTxtNumero->setNull       ( false );
    $obTxtNumero->setValue      ( isset($dadosCgm["numero"]) ? $dadosCgm["numero"] : "" );

    $obLblMunicipio = new Label;
    $obLblMunicipio->setName       ('stMunicipio');
    $obLblMunicipio->setRotulo     ('Município');
    $obLblMunicipio->setValue      ( $stMunicipio?$stMunicipio:"&nbsp;" );
    $obLblMunicipio->setId         ('stMunicipio');

    $obLblEstado = new Label;
    $obLblEstado->setName       ('stEstado');
    $obLblEstado->setRotulo     ('Estado');
    $obLblEstado->setValue      ( $stUF?$stUF:"&nbsp;" );
    $obLblEstado->setId         ('stEstado');

    $obHdnNomUf = new Hidden;
    $obHdnNomUf->setName       ('nomUf');
    $obHdnNomUf->setValue      ( isset($dadosCgm["nomUf"]) ? $dadosCgm["nomUf"] : "");

    $obHdnNomMunicipio = new Hidden;
    $obHdnNomMunicipio->setName       ('nomMunicipio');
    $obHdnNomMunicipio->setValue      ( isset($dadosCgm["nomMunicipio"]) ? $dadosCgm["nomMunicipio"] : "" );

    $obTxtComplemento = new TextBox;
    $obTxtComplemento->setName       ('stComplemento');
    $obTxtComplemento->setRotulo     ('Complemento');
    $obTxtComplemento->setNull       ( true );
    $obTxtComplemento->setValue      ( isset($dadosCgm["complemento"]) ? $dadosCgm["complemento"] : "");
    $obTxtComplemento->setMaxLength  ( 20 ); // sw_cgm::complemento::varchar(20)
    $obTxtComplemento->setSize       ( 35 ); // antes 50

    $rsBairro = new RecordSet;
    $rsCep = new RecordSet;
    $stLogradouroNome = "&nbsp;";

    if ( isset($dadosCgm["cod_logradouro"]) ) {
        include_once ( CAM_GT_CIM_NEGOCIO."RCIMTrecho.class.php" );
        //-----------------
        $obRCIMTrecho       = new RCIMTrecho;
        $obRCIMTrecho->setCodigoLogradouro( $dadosCgm["cod_logradouro"] ) ;
        $obRCIMTrecho->listarLogradourosTrecho( $rsLogradouro, "", $dadosCgm["cod_pais"] );
        if ( !$rsLogradouro->eof() ) {
            $stLogradouroNome = $rsLogradouro->getCampo("tipo_nome");
        }
        $obRCIMTrecho->listarBairroLogradouro( $rsBairro );
        $obRCIMTrecho->listarCEP( $rsCep );
        $arCep = $rsCep->getElementos();
        for ( $inX=0; $inX<count($arCep); $inX++ ) {
            $arCep[$inX]["cod_cep"] = $arCep[$inX]["cep"];
            $arCep[$inX]["num_cep"] = $arCep[$inX]["cep"];
        }

        $rsCep->preenche( $arCep );
    }

    $obBscLogradouro = new BuscaInner;
    $obBscLogradouro->setMonitorarCampoCod(true);
    $obBscLogradouro->setRotulo ( "Logradouro" );
    $obBscLogradouro->setTitle  ( "Logradouro onde o trecho está localizado" );
    $obBscLogradouro->setId     ( "campoInnerLogr" );
    $obBscLogradouro->setValue  ( $stLogradouroNome );
    $obBscLogradouro->setNull   ( false );
    $obBscLogradouro->obCampoCod->setName  ( "inNumLogradouro" );
    $obBscLogradouro->obCampoCod->setId    ( "inNumLogradouro" );
    $obBscLogradouro->obCampoCod->setValue ( isset($dadosCgm["cod_logradouro"]) ? $dadosCgm["cod_logradouro"] : "" );
    $obBscLogradouro->obCampoCod->obEvento->setOnChange( "javascript:buscaValor('666');" );
    $stBusca  = "abrePopUpCgm('".CAM_GT_CIM_POPUPS."logradouro/FLProcurarLogradouro.php','frm','inNumLogradouro','campoInnerLogr',''";
    $stBusca .= " ,'".Sessao::getId()."&stCadastro=Cgm','800','550')";
    $obBscLogradouro->setFuncaoBusca                    ( $stBusca );

    $obTxtCodBairro = new TextBox;
    $obTxtCodBairro->setRotulo    ( "Bairro"               );
    $obTxtCodBairro->setName      ( "inCodigoBairro"       );
    $obTxtCodBairro->setValue     ( isset($dadosCgm["cod_bairro"]) ? $dadosCgm["cod_bairro"] : "" );
    $obTxtCodBairro->setSize      ( 8                      );
    $obTxtCodBairro->setMaxLength ( 8                      );
    $obTxtCodBairro->setNull      ( false                  );
    $obTxtCodBairro->setInteiro   ( true                   );
    $obTxtCodBairro->obEvento->setOnChange( "javascript:CopiaDoTextoProComboBairro();" );

    $obCmbBairro = new Select;
    $obCmbBairro->setRotulo       ( "Bairro"               );
    $obCmbBairro->setId           ( "cmbBairro"            );
    $obCmbBairro->setName         ( "cmbBairro"            );
    $obCmbBairro->addOption       ( "", "Selecione"        );
    $obCmbBairro->setCampoId      ( "cod_bairro"           );
    $obCmbBairro->setCampoDesc    ( "nom_bairro"           );
    $obCmbBairro->preencheCombo   ( $rsBairro              );
    $obCmbBairro->setValue        ( isset($dadosCgm["cod_bairro"]) ? $dadosCgm["cod_bairro"] : "" );
    $obCmbBairro->setNull         ( false                  );
    $obCmbBairro->setStyle        ( "width: 220px"         );
    $obCmbBairro->obEvento->setOnChange ( "javascript:CopiaDoComboProTexto();" );


    $obCmbCep = new Select;
    $obCmbCep->setName         ( "cmbCEP"            );
    $obCmbCep->setId           ( "cmbCEP"            );
    $obCmbCep->setRotulo       ( "CEP"               );
    $obCmbCep->addOption       ( "", "Selecione"     );
    $obCmbCep->setCampoId      ( "cod_cep"           );
    $obCmbCep->setCampoDesc    ( "num_cep"           );
    $obCmbCep->preencheCombo   ( $rsCep              );
    $obCmbCep->setValue        ( isset($dadosCgm["cep"]) ? $dadosCgm["cep"] : "" );
    $obCmbCep->setNull         ( False               );
    $obCmbCep->setStyle        ( "width: 220px"      );

    $obHdnCEP = new Hidden;
    $obHdnCEP->setName ( "hdnCEP" );
    $obHdnCEP->setValue( "" );

    $obHdnCEPCorresp = new Hidden;
    $obHdnCEPCorresp->setName ( "hdnCEPCorresp" );
    $obHdnCEPCorresp->setValue( "" );

    $obHdnNomLogradouro = new Hidden;
    $obHdnNomLogradouro->setName ( "stNomeLogradouro" );
    $obHdnNomLogradouro->setValue( isset($_REQUEST ["stNomeLogradouro"]) ? $_REQUEST ["stNomeLogradouro"] : "" );

    $obHdnCodMunicipio = new Hidden;
    $obHdnCodMunicipio->setName ( "inCodMunicipio" );
    $obHdnCodMunicipio->setValue( isset($dadosCgm ["codMunicipio"]) ? $dadosCgm ["codMunicipio"] : "" );

    $obHdnCodUF = new Hidden;
    $obHdnCodUF->setName ( "inCodUF" );
    $obHdnCodUF->setValue( isset($dadosCgm ["codUf"]) ? $dadosCgm ["codUf"] : ""  );

    $obHdnCodLogradouro = new Hidden;
    $obHdnCodLogradouro->setName  ( "inCodLogradouro"            );
    $obHdnCodLogradouro->setValue ( isset($dadosCgm ["logradouro"]) ? $dadosCgm ["logradouro"] : "" );

    $obHdnNomLogradouro->montaHTML();
    $stHTML = $obHdnNomLogradouro->getHtml ();
    $obHdnCodMunicipio->montaHTML();
    $stHTML .= $obHdnCodMunicipio->getHtml ();
    $obHdnCodUF->montaHTML();
    $stHTML .= $obHdnCodUF->getHtml ();
    $obHdnCodLogradouro->montaHTML();
    $stHTML .= $obHdnCodLogradouro->getHtml ();
    $obHdnCEP->montaHTML();
    $stHTML .= $obHdnCEP->getHtml ();
    $obHdnCEPCorresp->montaHTML();
    $stHTML .= $obHdnCEPCorresp->getHtml ();
    $obHdnNomUf->montaHTML();
    $stHTML .= $obHdnNomUf->getHtml ();
    $obHdnNomMunicipio->montaHTML();
    $stHTML .= $obHdnNomMunicipio->getHtml ();


    echo $stHTML;

    $obLblEstado->montaHTML();
    $stHTML = $obLblEstado->getHtml ();

    echo "<tr>";
    echo "<td class=\"label\" title=\"Estado.\">*Estado</td>";
    echo "<td class=\"field\">".$stHTML."</td>";
    echo "</tr>";

    $obLblMunicipio->montaHTML();
    $stHTML = $obLblMunicipio->getHtml ();

    echo "<tr>";
    echo "<td class=\"label\" title=\"Cidade.\">*Cidade</td>";
    echo "<td class=\"field\">".$stHTML."</td>";
    echo "</tr>";

    $obBscLogradouro->montaHTML();
    $stHTML = $obBscLogradouro->getHtml ();

    echo "<tr>";
    echo "<td class=\"label\" title=\"Logradouro onde o trecho está localizado.\">*Logradouro</td>";
    echo "<td class=\"field\">".$stHTML."</td>";
    echo "</tr>";

    $obTxtNumero->montaHTML();
    $stHTML = $obTxtNumero->getHtml ();

    echo "<tr>";
    echo "<td class=\"label\" title=\"Número.\">*Número</td>";
    echo "<td class=\"field\">".$stHTML."</td>";
    echo "</tr>";


    $obTxtComplemento->montaHTML();
    $stHTML = $obTxtComplemento->getHtml ();
    echo "<tr>";
    echo "<td class=\"label\" title=\"Complemento.\">Complemento</td>";
    echo "<td class=\"field\">".$stHTML."</td>";
    echo "</tr>";

    $obTxtCodBairro->montaHTML();
    $stHTML = $obTxtCodBairro->getHtml ();

    $obCmbBairro->montaHTML();
    $stHTML .= $obCmbBairro->getHtml ();

    echo "<tr>";
    echo "<td class=\"label\" title=\"Bairro.\">*Bairro</td>";
    echo "<td class=\"field\">".$stHTML."</td>";
    echo "</tr>";

    $obCmbCep->montaHTML();
    $stHTML = $obCmbCep->getHtml ();

    echo "<tr>";
    echo "<td class=\"label\" title=\"CEP.\">*CEP</td>";
    echo "<td class=\"field\">".$stHTML."</td>";
    echo "</tr>";


    if ( isset($dadosCgm["apresentar_alteracao"]) && isset($dadosCgm["logradouroCorresp"]) ) {
        $obLblPais = new Label;
        $obLblPais->setName       ('lblPais');
        $obLblPais->setRotulo     ('País');
        $obLblPais->setValue      ( $dadosCgm["paisCorresp"]?$dadosCgm["paisCorresp"]:"&nbsp;" );
        $obLblPais->setId         ('lblPais');

        $obLblEstado = new Label;
        $obLblEstado->setName       ('lblEstado');
        $obLblEstado->setRotulo     ('Estado');
        $obLblEstado->setValue      ( $dadosCgm["estadoCorresp"]?$dadosCgm["estadoCorresp"]:"&nbsp;" );
        $obLblEstado->setId         ('lblEstado');

        $obLblMunicipio = new Label;
        $obLblMunicipio->setName       ('lblCidade');
        $obLblMunicipio->setRotulo     ('Cidade');
        $obLblMunicipio->setValue      ( $dadosCgm["municipioCorresp"]?$dadosCgm["municipioCorresp"]:"&nbsp;" );
        $obLblMunicipio->setId         ('lblCidade');

        $obLblLogradouro = new Label;
        $obLblLogradouro->setName       ('lblLogradouro');
        $obLblLogradouro->setRotulo     ('Logradouro');
        $obLblLogradouro->setValue      ( $dadosCgm["logradouroCorresp"]?$dadosCgm["logradouroCorresp"]:"&nbsp;" );
        $obLblLogradouro->setId         ('lblLogradouro');

        $obLblNumero = new Label;
        $obLblNumero->setName       ('lblNumero');
        $obLblNumero->setRotulo     ('Número');
        $obLblNumero->setValue      ( $dadosCgm["numeroCorresp"]?$dadosCgm["numeroCorresp"]:"&nbsp;" );
        $obLblNumero->setId         ('lblNumero');

        $obLblComplemento = new Label;
        $obLblComplemento->setName       ('lblComplemento');
        $obLblComplemento->setRotulo     ('Complemento');
        $obLblComplemento->setValue      ( $dadosCgm["complementoCorresp"]?$dadosCgm["complementoCorresp"]:"&nbsp;" );
        $obLblComplemento->setId         ('lblComplemento');

        $obLblBairro = new Label;
        $obLblBairro->setName       ('lblBairro');
        $obLblBairro->setRotulo     ('Bairro');
        $obLblBairro->setValue      ( $dadosCgm["bairroCorresp"]?$dadosCgm["bairroCorresp"]:"&nbsp;" );
        $obLblBairro->setId         ('lblBairro');

        $obLblCEP = new Label;
        $obLblCEP->setName       ('lblCep');
        $obLblCEP->setRotulo     ('CEP');
        $obLblCEP->setValue      ( $dadosCgm["cepCorresp"]?$dadosCgm["cepCorresp"]:"&nbsp;" );
        $obLblCEP->setId         ('lblCep');

?>
        <tr>
            <td colspan='2' class='alt_dados'>Dados de endereço para correspondência atual
            <span style='position: absolute;right:10px;'><img id='imgBotao' src='<?=CAM_FW_IMAGENS.'botao_expandir15px.png';?>' border='0' onClick='javascript:habilitaEnderecoCorrespondencia();'></span>
            </td>
        </tr>
        <tr>
           <td colspan='2'>
                <div id='endCorresp' style='display:none;'>
                    <table width='100%'>

<?php
        $obLblPais->montaHTML();
        $stHTML = $obLblPais->getHtml ();

        echo "<tr>";
        echo "<td class=\"label\" title=\"País.\">País</td>";
        echo "<td class=\"field\">".$stHTML."</td>";
        echo "</tr>";

        $obLblEstado->montaHTML();
        $stHTML = $obLblEstado->getHtml ();

        echo "<tr>";
        echo "<td class=\"label\" title=\"Estado.\">Estado</td>";
        echo "<td class=\"field\">".$stHTML."</td>";
        echo "</tr>";

        $obLblMunicipio->montaHTML();
        $stHTML = $obLblMunicipio->getHtml ();

        echo "<tr>";
        echo "<td class=\"label\" title=\"Cidade.\">Cidade</td>";
        echo "<td class=\"field\">".$stHTML."</td>";
        echo "</tr>";

        $obLblLogradouro->montaHTML();
        $stHTML = $obLblLogradouro->getHtml ();

        echo "<tr>";
        echo "<td class=\"label\" title=\"Logradouro.\">Logradouro</td>";
        echo "<td class=\"field\">".$stHTML."</td>";
        echo "</tr>";

        $obLblNumero->montaHTML();
        $stHTML = $obLblNumero->getHtml ();

        echo "<tr>";
        echo "<td class=\"label\" title=\"Número.\">Número</td>";
        echo "<td class=\"field\">".$stHTML."</td>";
        echo "</tr>";

        $obLblComplemento->montaHTML();
        $stHTML = $obLblComplemento->getHtml ();

        echo "<tr>";
        echo "<td class=\"label\" title=\"Complemento.\">Complemento</td>";
        echo "<td class=\"field\">".$stHTML."</td>";
        echo "</tr>";

        $obLblBairro->montaHTML();
        $stHTML = $obLblBairro->getHtml ();

        echo "<tr>";
        echo "<td class=\"label\" title=\"Bairro.\">Bairro</td>";
        echo "<td class=\"field\">".$stHTML."</td>";
        echo "</tr>";

        $obLblCEP->montaHTML();
        $stHTML = $obLblCEP->getHtml ();

        echo "<tr>";
        echo "<td class=\"label\" title=\"CEP.\">CEP</td>";
        echo "<td class=\"field\">".$stHTML."</td>";
        echo "</tr>";
?>
        <tr>
            <td colspan='2' class='alt_dados'>Dados de endereço para correspondência</td></tr>
        <tr>
<?php
    } else {
?>
        <tr>
            <td colspan='2' class='alt_dados'>Dados de endereço para correspondência
                <span style='position: absolute;right:10px;'><img id='imgBotao' src='<?=CAM_FW_IMAGENS.'botao_expandir15px.png';?>' border='0' onClick='javascript:habilitaEnderecoCorrespondencia();'></span>
            </td>
        </tr>
        <tr>
           <td colspan='2'>
        <div id='endCorresp' style='display:none;'>
        <table width='100%'>

<?php
    }
?>

        <tr>
            <td class="label">País</td>
            <td class="field">
                <select name="paisCorresp" onChange="javascript: atualizadados('estadoCorresp','2001')" style="width: 300px">
                    <option value="0">Selecione um País</option>
<?php
    $sSQL = "select cod_pais, nom_pais from sw_pais where cod_pais != 2 order by nom_pais";
    $dbEmp = new dataBaseLegado;
    $dbEmp->abreBD();
    $dbEmp->abreSelecao($sSQL);
    $dbEmp->vaiPrimeiro();
    $comboPaisCorresp = "";
    if (empty($dadosCgm['cod_paisCorresp'])) {
       $dadosCgm['cod_paisCorresp'] = $inCodpais = 1;
    } else {
        if (empty($dadosCgm['cod_pais'])) {
           $inCodpais = $codpais;
        } else {
           $inCodpais = $dadosCgm['cod_paisCorresp'];
        }
    }

    while (!$dbEmp->eof()) {
        $cod_paisCorresp  = trim($dbEmp->pegaCampo("cod_pais"));
        $nom_paisCorresp  = trim($dbEmp->pegaCampo("nom_pais"));
        $dbEmp->vaiProximo();
        if ($dadosCgm['cod_paisCorresp'] == $cod_paisCorresp) {
            $comboPaisCorresp .= " <option value=".$cod_paisCorresp." selected>".$nom_paisCorresp."</option>\n";
        } else {
            if ($codpaisCorresp == $cod_paisCorresp) {
                $comboPaisCorresp .= " <option value=".$cod_paisCorresp." selected>".$nom_paisCorresp."</option>\n";
            } else {
                $comboPaisCorresp .= " <option value=".$cod_paisCorresp.">".$nom_paisCorresp."</option>\n";
            }
        }
    }
    $dbEmp->limpaSelecao();
    $dbEmp->fechaBD();
    echo "$comboPaisCorresp";
?>
                </select>
            </td>
        </tr>

<?php
    include_once ( CAM_GT_CIM_NEGOCIO."RCIMTrecho.class.php" );
    $obRCIMTrecho  = new RCIMTrecho;

    $obTxtNumero = new TextBox;
    $obTxtNumero->setName       ('inNumeroCorresp');
    $obTxtNumero->setRotulo     ('Número');
    $obTxtNumero->setInteiro    ( false );
    $obTxtNumero->setMaxLength  ( 6 );
    $obTxtNumero->setSize       ( 8 );
    $obTxtNumero->setNull       ( false );
    $obTxtNumero->setValue      ( $dadosCgm["numeroCorresp"] );

    $obLblMunicipio = new Label;
    $obLblMunicipio->setName       ('stMunicipioCorresp');
    $obLblMunicipio->setRotulo     ('Município');
    $obLblMunicipio->setValue      ( $stMunicipioCorresp?$stMunicipioCorresp:"&nbsp;" );
    $obLblMunicipio->setId         ('stMunicipioCorresp');
    $obLblMunicipio->setStyle      ('margin:0');

    $obLblEstado = new Label;
    $obLblEstado->setName       ('stEstadoCorresp');
    $obLblEstado->setRotulo     ('Estado');
    $obLblEstado->setValue      ( $stUFCorresp?$stUFCorresp:"&nbsp;" );
    $obLblEstado->setId         ('stEstadoCorresp');
    $obLblEstado->setStyle      ('margin:0');

    $obTxtComplemento = new TextBox;
    $obTxtComplemento->setName       ('stComplementoCorresp');
    $obTxtComplemento->setRotulo     ('Complemento');
    $obTxtComplemento->setNull       ( true );
    $obTxtComplemento->setValue      ( $stComplemento );
    $obTxtComplemento->setMaxLength  ( 20 ); // sw_cgm::complemento_corresp::varchar(20)
    $obTxtComplemento->setSize       ( 35 ); // antes 50
    $obTxtComplemento->setValue      ( $dadosCgm["complementoCorresp"] );


    $rsBairro = new RecordSet;
    $rsCep = new RecordSet;
    $stLogradouroNome = "&nbsp;";
    if ($dadosCgm["cod_logradouro_corresp"]) {
        include_once ( CAM_GT_CIM_NEGOCIO."RCIMTrecho.class.php" );
        //-----------------
        $obRCIMTrecho       = new RCIMTrecho;
        $obRCIMTrecho->setCodigoLogradouro( $dadosCgm["cod_logradouro_corresp"] ) ;
        $obRCIMTrecho->listarLogradourosTrecho( $rsLogradouro, "", $dadosCgm["cod_pais"] );
        if ( !$rsLogradouro->eof() ) {
            $stLogradouroNome = $rsLogradouro->getCampo("tipo_nome");
        }

        $obRCIMTrecho->listarBairroLogradouro( $rsBairro );
        $obRCIMTrecho->listarCEP( $rsCep );
        $arCep = $rsCep->getElementos();
        for ( $inX=0; $inX<count($arCep); $inX++ ) {
            $arCep[$inX]["cod_cep"] = $arCep[$inX]["cep"];
            $arCep[$inX]["num_cep"] = $arCep[$inX]["cep"];
        }

        $rsCep->preenche( $arCep );
    }


    $obBscLogradouro = new BuscaInner;
    $obBscLogradouro->setMonitorarCampoCod(true);
    $obBscLogradouro->setRotulo ( "Logradouro"                               );
    $obBscLogradouro->setTitle  ( "Logradouro onde o trecho está localizado" );
    $obBscLogradouro->setId     ( "campoInnerLogrCorresp"                    );
    $obBscLogradouro->setValue  ( $stLogradouroNome );
    $obBscLogradouro->setNull   ( false );
    $obBscLogradouro->obCampoCod->setName  ( "inNumLogradouroCorresp"        );
    $obBscLogradouro->obCampoCod->setValue ( $dadosCgm["cod_logradouro_corresp"]!="0"?$dadosCgm["cod_logradouro_corresp"]:"" );
    $obBscLogradouro->obCampoCod->obEvento->setOnChange( "javascript:buscaValor('667');" );
    $stBusca  = "abrePopUpCgmCorresp('".CAM_GT_CIM_POPUPS."logradouro/FLProcurarLogradouro.php','frm','inNumLogradouroCorresp','campoInnerLogrCorresp',''";
    $stBusca .= " ,'".Sessao::getId()."&stCadastro=CgmCorresp','800','550')";
    $obBscLogradouro->setFuncaoBusca ( $stBusca );

    $obTxtCodBairro = new TextBox;
    $obTxtCodBairro->setRotulo    ( "Bairro"               );
    $obTxtCodBairro->setName      ( "inCodigoBairroCorresp" );
    $obTxtCodBairro->setValue     ( $dadosCgm["cod_bairro_corresp"] );
    $obTxtCodBairro->setSize      ( 8                      );
    $obTxtCodBairro->setMaxLength ( 8                      );
    $obTxtCodBairro->setNull      ( false                  );
    $obTxtCodBairro->setInteiro   ( true                   );
    $obTxtCodBairro->obEvento->setOnChange( "javascript:CopiaDoTextoProComboBairroCorresp();" );

    $obCmbBairro = new Select;
    $obCmbBairro->setRotulo       ( "Bairro"               );
    $obCmbBairro->setName         ( "cmbBairroCorresp"            );
    $obCmbBairro->addOption       ( "", "Selecione"        );
    $obCmbBairro->setCampoId      ( "cod_bairro"           );
    $obCmbBairro->setCampoDesc    ( "nom_bairro"           );
    $obCmbBairro->preencheCombo   ( $rsBairro              );
    $obCmbBairro->setValue        ( $dadosCgm["cod_bairro_corresp"] );
    $obCmbBairro->setNull         ( false                  );
    $obCmbBairro->setStyle        ( "width: 220px"         );
    $obCmbBairro->obEvento->setOnChange ( "javascript:CopiaDoComboProTextoCorresp();" );

    $obCmbCep = new Select;
    $obCmbCep->setName         ( "cmbCEPCorresp" );
    $obCmbCep->setRotulo       ( "CEP"               );
    $obCmbCep->addOption       ( "", "Selecione"     );
    $obCmbCep->setCampoId      ( "cod_cep"           );
    $obCmbCep->setCampoDesc    ( "num_cep"           );
    $obCmbCep->preencheCombo   ( $rsCep              );
    $obCmbCep->setValue        ( $dadosCgm["cepCorresp"] );
    $obCmbCep->setNull         ( False               );
    $obCmbCep->setStyle        ( "width: 220px"      );

    $obHdnNomLogradouro = new Hidden;
    $obHdnNomLogradouro->setName ( "stNomeLogradouroCorresp" );
    $obHdnNomLogradouro->setValue( $_REQUEST ["stNomeLogradouroCorresp"] );

    $obHdnCodMunicipio = new Hidden;
    $obHdnCodMunicipio->setName ( "inCodMunicipioCorresp" );
    $obHdnCodMunicipio->setValue( $dadosCgm ["codMunicipioCorresp"] );

    $obHdnCodUF = new Hidden;
    $obHdnCodUF->setName ( "inCodUFCorresp" );
    $obHdnCodUF->setValue( $dadosCgm ["codUfCorresp"] );

    $obHdnCodLogradouro = new Hidden;
    $obHdnCodLogradouro->setName  ( "inCodLogradouroCorresp"            );
    $obHdnCodLogradouro->setValue ( $dadosCgm ["logradouroCorresp"] );

    $obHdnNomLogradouro->montaHTML();
    $stHTML = $obHdnNomLogradouro->getHtml ();
    $obHdnCodMunicipio->montaHTML();
    $stHTML .= $obHdnCodMunicipio->getHtml ();
    $obHdnCodUF->montaHTML();
    $stHTML .= $obHdnCodUF->getHtml ();
    $obHdnCodLogradouro->montaHTML();
    $stHTML .= $obHdnCodLogradouro->getHtml ();

    echo $stHTML;

    $obLblEstado->montaHTML();
    $stHTML = $obLblEstado->getHtml ();

    echo "<tr>";
    echo "<td class=\"label\" title=\"Estado.\">Estado</td>";
    echo "<td class=\"field\">".$stHTML."</td>";
    echo "</tr>";

    $obLblMunicipio->montaHTML();
    $stHTML = $obLblMunicipio->getHtml ();

    echo "<tr>";
    echo "<td class=\"label\" title=\"Cidade.\">Cidade</td>";
    echo "<td class=\"field\">".$stHTML."</td>";
    echo "</tr>";

    $obBscLogradouro->montaHTML();
    $stHTML = $obBscLogradouro->getHtml ();

    if ( isset($dadosCgm["apresentar_alteracao"]) ) {
        $stObrigatorio = "*";
    }else
        $stObrigatorio = "";

    echo "<tr>";
    echo "<td class=\"label\" title=\"Logradouro onde o trecho está localizado.\">".$stObrigatorio."Logradouro</td>";
    echo "<td class=\"field\">".$stHTML."</td>";
    echo "</tr>";

    $obTxtNumero->montaHTML();
    $stHTML = $obTxtNumero->getHtml ();

    echo "<tr>";
    echo "<td class=\"label\" title=\"Número.\">".$stObrigatorio."Número</td>";
    echo "<td class=\"field\">".$stHTML."</td>";
    echo "</tr>";


    $obTxtComplemento->montaHTML();
    $stHTML = $obTxtComplemento->getHtml ();
    echo "<tr>";
    echo "<td class=\"label\" title=\"Complemento.\">Complemento</td>";
    echo "<td class=\"field\">".$stHTML."</td>";
    echo "</tr>";

    $obTxtCodBairro->montaHTML();
    $stHTML = $obTxtCodBairro->getHtml ();

    $obCmbBairro->montaHTML();
    $stHTML .= $obCmbBairro->getHtml ();

    echo "<tr>";
    echo "<td class=\"label\" title=\"Bairro.\">".$stObrigatorio."Bairro</td>";
    echo "<td class=\"field\">".$stHTML."</td>";
    echo "</tr>";

    $obCmbCep->montaHTML();
    $stHTML = $obCmbCep->getHtml ();

    echo "<tr>";
    echo "<td class=\"label\" title=\"CEP.\">".$stObrigatorio."CEP</td>";
    echo "<td class=\"field\">".$stHTML."</td>";
    echo "</tr>";
?>


</table>
</div>
</td>
</tr>






        <tr>
            <td colspan='2' class='alt_dados'>Dados para contato</td>
        </tr>
<?php
if ( $pessoa == 'fisica' or ( $pessoa == 'outros' and $tipo == 'fisica' )) {
    $stRotuloResidencial = "Telefone residencial";
    $stRotuloComercial   = "Telefone comercial";
} else {
    $stRotuloResidencial = "Telefone";
    $stRotuloComercial   = "Fax";
}
?>
        <tr>
            <td class="label"><?=$stRotuloResidencial;?></td>
            <td class="field">
        <input type="text" placeholder="DD" name="dddRes" maxlength="2" size="2" value="<?=$dddRes;?>"
         onKeyUp="return autoTab(this, 2, event);" onKeyPress="return(isValido(this,event,'0123456789'));">&nbsp;
                <input type="text" placeholder="N° Telefone" name="foneRes" maxlength="8" size="8" value="<?=$foneRes;?>"
                onKeyUp="return autoTab(this, 8, event);" onKeyPress="return(isValido(this,event,'0123456789'));">&nbsp;
<?php
    $ramalCom = isset($ramalCom) ? $ramalCom : "";
    $email = isset($email) ? $email : "";
    $emailAdic = isset($emailAdic) ? $emailAdic : "";
    $ramalRes = isset($ramalRes) ? $ramalRes : "" ;
    $site = isset($site) ? $site : "" ;
if ($pessoa == 'juridica') {
?>
                <b> Ramal</b>&nbsp;
                <input type="text" name="ramalRes" size="4" maxlength="4" value="<?=$ramalRes?>" onKeyUp="return autoTab(this, 4, event);" onKeyPress="return(isValido(this,event,'0123456789'));">
<?php
}
?>
        </td>
        </tr>
        <tr>
            <td class="label"><?=$stRotuloComercial;?></td>
            <td class="field">
            <input type="text" placeholder="DD" name="dddCom" maxlength="2" size="2" value="<?=$dddCom;?>"
        onKeyUp="return autoTab(this, 2, event);" onKeyPress="return(isValido(this,event,'0123456789'));">&nbsp;
                <input type="text" placeholder="N° Telefone" name="foneCom" maxlength="8" size="8" value="<?=$foneCom;?>"
                onKeyUp="return autoTab(this, 8, event);" onKeyPress="return(isValido(this,event,'0123456789'));">&nbsp;<b> Ramal</b>&nbsp;
                <input type="text" name="ramalCom" size="4" maxlength="4" value="<?=$ramalCom?>" onKeyUp="return autoTab(this, 4, event);" onKeyPress="return(isValido(this,event,'0123456789'));">
            </td>
        </tr>
        <tr>
            <td class="label">Telefone celular</td>
            <td class="field">
            <input type="text" placeholder="DD" name="dddCel" maxlength="2" size="2" value="<?=$dddCel;?>"
        onKeyUp="return autoTab(this, 2, event);" onKeyPress="return(isValido(this,event,'0123456789'));">&nbsp;
                <input type="text" placeholder="N° Telefone" name="foneCel" maxlength="9" size="8" value="<?=$foneCel;?>"
                onKeyUp="return autoTab(this, 9, event);" onKeyPress="return(isValido(this,event,'0123456789'));">
            </td>
        </tr>
        <tr>
            <td class="label">e-mail</td>
            <td class="field">
                <input type="text" name="email" maxlength="100" size="30" value="<?=$email;?>"
                onKeyUp="return autoTab(this, 100, event);"></td>
        </tr>
        <tr>
            <td class="label">e-mail adicional</td>
            <td class="field">
                <input type="text" name="emailAdic" maxlength="100" size="30" value="<?=$emailAdic;?>"
                onKeyUp="return autoTab(this, 100, event);"></td>
        </tr>
        <tr>
            <td class="label" width="30%" title="Site">Site</td>
            <td class="field" width="70%">
                <input type="text" name="stSite" maxlength="200" size="50" value="<?=$site;?>" onKeyUp="return autoTab(this, 200, event);">
            </td>
        </tr>
<?php
    $this->geraCamposAtributo( $numCgm );
?>
        <tr>
            <td colspan='2' class='field'>
<?php
global $stAcao;
if ($stAcao == "inclui") {
?>

<table width="100%" cellspacing=0 border=0 cellpadding=0>
    <tr>
        <td>
            <input type="button" name="ok" value="OK" style="width: 60px" onClick="Salvar();" >&nbsp;
            <input type="button" name="limpar" value="Limpar" style="width: 60px" onClick="Limpar();">
        </td>
        <td class="fieldright_noborder">
        </td>
    </tr>
</table>

<?php
} else {
?>

<table width="100%" cellspacing=0 border=0 cellpadding=0>
    <tr>
        <td>
            <input type="button" name="ok" value="OK" style="width: 60px" onClick="Salvar();" >&nbsp;
            <input type="button" name="cancelar" value="Cancelar" style="width: 70px" onClick="Cancela();" >&nbsp;

<?php
            $sQl = pegaDado("numcgm", "sw_cgm_logradouro"," where numcgm = ". $dadosCgm['numCgm']);
                if (!$sQl) {
?>
                    <input type="button" name="limpar" value="Limpar" style="width: 60px" onClick="Limpar();">
<?php
                }
?>

        </td>
        <td class="fieldright_noborder">
            <b>* Campos obrigatórios</b>
        </td>
    </tr>
</table>

<?php
}
?>
            </td>
        </tr>
    </table>
    </form>
    <script type='text/javascript'>
    <!--
        document.frm.nomCgm.focus();
    //-->
    </script>
    <iframe name="oculto_nome" height="0" width="0">
<?php
    }//Fim function formCgm


/***************************************************************************
Gera o HTML com os campos de formulario para os atributos (se houver)
/**************************************************************************/
    public function geraCamposAtributo($inNumCGM)
    {
        $obCGM = new cgmLegado;
        $arAtributoCGM = $obCGM->buscaAtributosCGM( $inNumCGM );
        $stHtml = isset($stHtml) ? $stHtml : "";
    $stHtml .= "<tr>\n";
        $stHtml .= "    <td colspan='2' class='alt_dados'>Atributos</td>\n";
        $stHtml .= "</tr>\n";
        foreach ($arAtributoCGM as $arAtributos) {
            if ($arAtributos["numCgm"]) {
                $valor = $arAtributos["valor"];
            } else {
                $valor = $arAtributos["valorPadrao"];
            }
            switch ($arAtributos["tipo"]) {
                case "t":
                    $sCampo = "<input type='text' size='40' name='atributo[".$arAtributos["codAtributo"]."]' value='".$valor."'>\n";
                break;
                case "n":
                    $sCampo  = "<input type='text' size='40' name='atributo[".$arAtributos["codAtributo"]."]' value='".$valor."'";
                    $sCampo .= " onKeyPress=\"return(isValido(this, event, '0123456789'));\">\n";
                break;
                case "l":
                    $arOption = explode("\n",$arAtributos["valorPadrao"]);
                    $sCampo  = "<select name='atributo[".$arAtributos["codAtributo"]."]' style='width: 300px'>\n";
                    $sCampo .= "    <option value='xxx'>Selecione um(a) ".$arAtributos["nomAtributo"]."</option>\n";
                    foreach ($arOption as $stOption) {
                        if (trim($valor) == trim($stOption)) {
                            $sCampo .= "    <option value='".trim($stOption)."' selected>".trim($stOption)."</option>\n";
                        } else {
                            $sCampo .= "    <option value='".trim($stOption)."'>".trim($stOption)."</option>\n";
                        }

                    }
                    $sCampo .= "</select>\n";
                break;
            }
            $stHtml .= "<tr>\n";
            $stHtml .= "    <td class='label'>".$arAtributos["nomAtributo"]."</td>\n";
            $stHtml .= "    <td class='field'>".$sCampo."</td>\n";
            $stHtml .= "</tr>\n";
        }
        echo $stHtml;
    }

/***************************************************************************
Gera o HTML para os atributos sem os campos de formulario(se houver)
/**************************************************************************/
    public function geraCamposAtributoLista($inNumCGM)
    {
        $obCGM = new cgmLegado;
        $arAtributoCGM = $obCGM->buscaAtributosCGM( $inNumCGM );
        $stHtml .= "<tr>\n";
        $stHtml .= "    <td colspan='2' class='alt_dados'>Atributos</td>\n";
        $stHtml .= "</tr>\n";
        foreach ($arAtributoCGM as $arAtributos) {
            $valor = $arAtributos["valor"];
            $stHtml .= "<tr>\n";
            $stHtml .= "    <td class='label'>".$arAtributos["nomAtributo"]."&nbsp;</td>\n";
            $stHtml .= "    <td class='field'>".$valor."&nbsp;</td>\n";
            $stHtml .= "</tr>\n";
        }
        echo $stHtml;
    }

/***************************************************************************
Gera o HTML para os atributos sem os campos de formulario(se houver)
/**************************************************************************/
    public function geraCamposAtributoListaCGA($inNumCGA, $timestamp)
    {
        $obCGM = new cgmLegado;
        $arAtributoCGM = $obCGM->buscaAtributosCGA( $inNumCGA, $timestamp );
        $stHtml .= "<tr>\n";
        $stHtml .= "    <td colspan='2' class='alt_dados'>Atributos de CGA</td>\n";
        $stHtml .= "</tr>\n";
        foreach ($arAtributoCGM as $arAtributos) {
            $valor = $arAtributos["valor"];
            $stHtml .= "<tr>\n";
            $stHtml .= "    <td class='label'>".$arAtributos["nomAtributo"]."&nbsp;</td>\n";
            $stHtml .= "    <td class='field'>".$valor."&nbsp;</td>\n";
            $stHtml .= "</tr>\n";
        }
        echo $stHtml;
    }

/***************************************************************************
Monta o formulário com os dados para cadastro de CGM
Se a variável $dados Cgm for maior que zero ele carrega também os dados do CGM
/**************************************************************************/
    public function listaDadosCgm($dadosCgm="",$action="",$controle=0)
    { 
        if (is_array($dadosCgm)) {
            //Grava como variável o nome da chave do vetor com o seu respectivo valor
            foreach ($dadosCgm as $campo=>$valor) {
                $$campo = trim($valor);
            }
            //Carrega o cep em partes para preencher os campos segmentados
            if (isset($cep)) {
            if ($cep) {
                    $cep = preg_replace("/[^0-9a-zA-Z]/", "", $cep);
                    $cep1 = substr($cep,0,5);
                    $cep2 = substr($cep,5,3);
                    $cep = $cep1."-".$cep2;
        }
            }
            if (isset($cepCorresp)) {
            if ($cepCorresp) {
                    $cepCorresp = preg_replace( "/[^0-9a-zA-Z]/", "", $cepCorresp);
                    $cepCorresp1 = substr($cepCorresp,0,5);
                    $cepCorresp2 = substr($cepCorresp,5,3);
                    $cepCorresp = $cepCorresp1."-".$cepCorresp2;
        }
            }
        if (!isset($dddRes)) {
                $dddRes = substr($foneRes,0,2);
                if ($foneRes) {
                $foneRes = substr($foneRes,2,10);
            }
        }
            if (!isset($dddCom)) {
                $dddCom = substr($foneCom,0,2);
                //$foneCom = substr($foneCom,2,8);
                if ($foneCom) {
                $foneCom = substr($foneCom,2,10);
            }

        }
            if (isset($ramalCom)) {
            if ($ramalCom) {
                     $ramalCom = " Ramal ".$ramalCom;
                }
        }


            if (!isset($dddCel)) {
                $dddCel = substr($foneCel,0,2);
                $foneCel = substr($foneCel,2,10);
            }

            //Carrega o cpf em partes para preencher os campos segmentados
            if (isset($cpf)) {
            if ($cpf) {
                    $cpf1 = substr($cpf,0,3);
                    $cpf2 = substr($cpf,3,3);
                    $cpf3 = substr($cpf,6,3);
                    $cpf4 = substr($cpf,9,2);
                $cpf = $cpf1.".".$cpf2.".".$cpf3."-".$cpf4;
        }
            }


        if (isset($dtCadastro)) {
                 if ($dtCadastro) {
                     $arDtCadastro = explode("-",$dtCadastro);
             $dtCadastro =  $arDtCadastro[2]."/".$arDtCadastro[1]."/".$arDtCadastro[0];
         }
        }

            //Carrega o cnpj em partes para preencher os campos segmentados
            if (isset($cnpj)) {
            if ($cnpj) {
                    $cnpj1 = substr($cnpj,0,2);
                    $cnpj2 = substr($cnpj,2,3);
                    $cnpj3 = substr($cnpj,5,3);
                    $cnpj4 = substr($cnpj,8,4);
                    $cnpj5 = substr($cnpj,12,2);
                $cnpj = $cnpj1.".".$cnpj2.".".$cnpj3."/".$cnpj4."-".$cnpj5;
                }
            }
        }
?>
    <script type="text/javascript">
    <!--
    function Voltar()
    {
        document.frm.action += "&pagina=<?=$_GET['pg'];?>&pg=<?=$_GET['pg'];?>&pos=<?=$_GET['pos'];?>&volta=true";
        document.frm.controle.value = 2;
        document.frm.submit();
    }
    //-->
    </script>
    <form name='frm' method='post' action='<?=$action;?>?<?=Sessao::getId();?>'>
    <input type="hidden" name="controle" value='<?=$controle;?>'>
    <input type="hidden" name="numCgm" value='<?=$numCgm;?>'>
    <input type="hidden" name="pessoa" value='<?=$pessoa;?>'>
    <input type="hidden" name="codUF" value='<?=$codUf;?>'>
    <input type="hidden" name="codMunicipio" value='<?=$codMunicipio;?>'>
    <input type="hidden" name="codUfCorresp" value='<?=$codUfCorresp;?>'>
    <input type="hidden" name="codMunicipioCorresp" value='<?=$codMunicipioCorresp;?>'>
    <table width="100%">
        <tr>
            <td class=alt_dados colspan=2>Dados para CGM</td>
        </tr>
        <tr>
            <td class="label" width="30%">CGM</td>
            <td class="field" width="70%">
                <?=$numCgm;?>&nbsp;
            </td>
        </tr>

        <tr>
            <td class="label" width="30%">Nome</td>
            <td class="field" width="70%">
                <?=$nomCgm;?>&nbsp;
            </td>
        </tr>
<?php if ($pessoa == 'juridica' or $tipo == 'juridica') { ?>
        <tr>
            <td class="label">CNPJ</td>
            <td class="field">
                <?=$cnpj;?>&nbsp;
            </td>
        </tr>
        <tr>
            <td class="label">Inscrição estadual</td>
            <td class="field">
                <?=$inscEst;?>&nbsp;
            </td>
        </tr>
    <tr>
            <td class="label">Nome Fantasia</td>
            <td class="field">
        <?php
        echo pegaDado( "nom_fantasia", "sw_cgm_pessoa_juridica", " where numcgm = ".$numCgm );
        ?>&nbsp;
            </td>
        </tr>
<?php } ?>
<?php
     if ($pessoa == 'fisica' or $tipo == 'fisica') {
?>
        <tr>
            <td class="label">CPF</td>
            <td class="field">
                <?=$cpf;?>&nbsp;
            </td>
        </tr>
        <tr>
            <td class="label">RG</td>
            <td class="field">
                <?=$rg;?>&nbsp;
            </td>
        </tr>
        <tr>
            <td class="label">Órgão emissor</td>
            <td class="field">
                <?=$inCodUFOrgaoEmissor;?> / <?=pegaDado( "nom_uf", "sw_uf", " where cod_uf = ".$inCodUFOrgaoEmissor );?>
            </td>
        </tr>
        <tr>
            <td class="label">Data da emissão</td>
            <td class="field">
                <?=$dtEmissaoRg;?>&nbsp;
            </td>
        </tr>
        <tr>
            <td class="label">Número CNH</td>
            <td class="field">
                <?=$numCnh?>&nbsp;
            </td>
        </tr>
        <tr>
            <td class="label">Categoria de habilitação</td>
            <td class="field">
                <?=$catHabilitacao = pegaDado( "nom_categoria" , "sw_categoria_habilitacao"," where cod_categoria = ".$catHabilitacao );?>&nbsp;
            </td>
        </tr>
        <tr>
            <td class="label">Data de validade da CNH</td>
            <td class="field">
                <?=$dtValidadeCnh;?>&nbsp;
            </td>
        </tr>

        <tr>
            <td class="label">PIS/PASEP</td>
            <td class="field">
                <?=$stPisPasep = pegaDado( "servidor_pis_pasep" ,"sw_cgm_pessoa_fisica", " where numcgm = ".$numCgm );?>&nbsp;
            </td>
        </tr>

    <tr>
            <td class="label">Nacionalidade</td>
            <td class="field">
        <?php
        echo pegaDado("nacionalidade", "sw_pais", " where cod_pais = " . pegaDado("cod_nacionalidade", "sw_cgm_pessoa_fisica", " where numcgm = ".$numCgm ) );
        ?>&nbsp;
            </td>
        </tr>
    <tr>
            <td class="label">Escolaridade</td>
            <td class="field">
        <?php
                if ($cod_escolaridade > 0) {
                    echo pegaDado("descricao", "sw_escolaridade", " where cod_escolaridade = ". $cod_escolaridade);
                }
        ?>&nbsp;
            </td>
        </tr>

<?php } ?>
        <tr>
            <td colspan='2' class='alt_dados'>Dados de endereço</td></tr>
        <tr>
            <td class="label">Endereço</td>
            <td class="field">
                <?=$endereco;?>&nbsp;
            </td>
        </tr>
        <tr>
            <td class="label">País</td>
            <td class="field">
                <?=$pais;?>&nbsp;
            </td>
        </tr>
        <tr>
            <td class="label">Estado</td>
            <td class="field">
                <?=$estado;?>&nbsp;
            </td>
        </tr>
        <tr>
            <td class="label">Cidade</td>
            <td class="field">
                <?=$municipio;?>&nbsp;
            </td>
        </tr>
        <tr>
            <td class="label">Bairro</td>
            <td class="field">
                <?=$bairro;?>&nbsp;
            </td>
        </tr>
        <tr>
            <td class="label">CEP</td>
            <td class="field">
                <?=$cep;?>&nbsp;
            </td>
        </tr>

        <?php
        // Verifica se o CGM utiliza as tabelas novas para cadastro de logradouro, na consulta de CGM
        $sQl = pegaDado("numcgm", "sw_cgm_logradouro"," where numcgm = ". $_REQUEST['numCgm']);

        if (!$sQl) {
            echo "<tr>";
            echo "      <td colspan='2' class='label'>
                            <Center align='center'>
                                <font color='CC0000'>
                                    Este CGM não possui o seu endereço atualizado, entre na ação 'Alterar CGM' e siga as orientações.
                                </font>
                            </Center>
                        </td>";
            echo "</tr>";
        }
        ?>

        <tr>
            <td colspan='2' class='alt_dados'>Dados de endereço para correspondência</td></tr>
        <tr>
            <td class="label">Endereço</td>
            <td class="field">
                <?=$enderecoCorresp;?>&nbsp;
            </td>
        </tr>
        <tr>
            <td class="label">País</td>
            <td class="field">
                <?=$paisCorresp;?>&nbsp;
            </td>
        </tr>
        <tr>
            <td class="label">Estado</td>
            <td class="field">
                <?=$estadoCorresp;?>&nbsp;
            </td>
        </tr>
        <tr>
            <td class="label">Cidade</td>
            <td class="field">
                <?=$municipioCorresp;?>&nbsp;
            </td>
        </tr>
        <tr>
            <td class="label">Bairro</td>
            <td class="field">
                <?=$bairroCorresp;?>&nbsp;
            </td>
        </tr>
        <tr>
            <td class="label">CEP</td>
            <td class="field">
                <?=$cepCorresp;?>&nbsp;
            </td>
        </tr>
        <tr>
            <td colspan='2' class='alt_dados'>Dados para contato</td>
        </tr>
        <tr>
            <td class="label">Telefone residencial</td>
            <td class="field">
                <?php echo $dddRes ? $dddRes." " : "";?>
                <?=$foneRes;?>
                <?=$ramalRes?>&nbsp;
            </td>
        </tr>
        <tr>
            <td class="label">Telefone comercial</td>
            <td class="field">
                <?php echo $dddCom ? $dddCom." " : "";?>
                <?=$foneCom;?>
                <?=$ramalCom?>&nbsp;
            </td>
        </tr>
        <tr>
            <td class="label">Telefone celular</td>
            <td class="field">
                <?php echo $dddCel ? $dddCel." " : "";?>
                <?=$foneCel;?>
            </td>
        </tr>
        <tr>
            <td class="label">e-mail</td>
            <td class="field">
                <?=$email;?>&nbsp;
            </td>
        </tr>
        <tr>
            <td class="label">e-mail adicional</td>
            <td class="field">
                <?=$emailAdic;?>&nbsp;
            </td>
        </tr>
        <tr>
            <td class="label">Site</td>
            <td class="field">
                <?=$site;?>&nbsp;
            </td>
        </tr>
<?php
    $this->geraCamposAtributoLista( $numCgm );
?>
    <tr>
            <td colspan='2' class='alt_dados'>Dados de cadastro</td>
        </tr>
        <tr>
            <td class="label">Data do cadastro</td>
            <td class="field">
            <?=$dtCadastro;?>&nbsp;
            </td>
        </tr>
    <tr>
            <td class="label">Responsável</td>
            <td class="field">
           <?=$nomResp;?>&nbsp;
            </td>
        </tr>
    <tr>
        <td colspan='2' class='field'>
            <input type="button" value="Voltar" onClick="JavaScript: Voltar()">
        </td>
        </tr>
    </table>
    </form>
<?php
    }//Fim function formCgm


/***************************************************************************
Monta tela com os dados do CGM a ser excluido
/**************************************************************************/
    public function formCgmExcluir($dadosCgm="")
    {
        if (is_array($dadosCgm)) {
            //Grava como variável o nome da chave do vetor com o seu respectivo valor
            foreach ($dadosCgm as $campo=>$valor) {
                $$campo = trim($valor);
            }

            //Carrega o cep em partes para preencher os campos segmentados
            $cep1 = substr($cep,0,5);
            $cep2 = substr($cep,5,3);
            $cepCorresp1 = substr($cepCorresp,0,5);
            $cepCorresp2 = substr($cepCorresp,5,3);

            //Carrega os telefones em partes para preencher os campos segmentados
            $dddRes = substr($foneRes,0,2);
            $foneRes = substr($foneRes,2,8);
            $dddCom = substr($foneCom,0,2);
            $foneCom = substr($foneCom,2,8);
            $dddCel = substr($foneCel,0,2);
            $foneCel = substr($foneCel,2,8);

        }
?>
        <script type="text/javascript">
            function excluirCgm()
            {
                var cgm = <?=$numCgm;?>;
                var objeto = "<?=$nomCgm;?>";

                alertaQuestao('../cgm/manutencao/excluiCgm.php','excluir',cgm,objeto,'sn_excluir','<?=Sessao::getId();?>');
            }
        </script>
            <table width="90%">
                <tr>
                    <td class="label" width="40%">Nome</td>
                    <td class="field" width="60%"><?=$nomCgm;?></td>
                </tr>
                <tr>
                    <td class="label">Dados de endereço</td>
                    <td class="field"><?=$tipoLogradouro;?>&nbsp;<?=$logradouro;?>&nbsp;<?=$numero;?>&nbsp;<?=$complemento;?></td>
                </tr>
                <tr>
                    <td class="label">Estado</td>
                    <td class="field"><?=$estado;?></td>
                </tr>
                <tr>
                    <td class="label">Cidade</td>
                    <td class="field"><?=$municipio;?></td>
                </tr>
                <tr>
                    <td class="label">Bairro</td>
                    <td class="field"><?=$bairro;?></td>
                </tr>
                <tr>
                    <td class="label">CEP</td>
                    <td class="field"><?php echo formataCep($cep); ?></td>
                </tr>
                <tr>
                    <td class="label">Endereço</td>
                    <td class="field"><?=$tipoLogradouroCorresp;?>&nbsp;<?=$logradouroCorresp;?>&nbsp;<?=$numeroCorresp;?>&nbsp;<?=$complementoCorresp;?></td>
                </tr>
                <tr>
                    <td class="label">Estado</td>
                    <td class="field"><?=$estadoCorresp;?></td>
                </tr>
                <tr>
                    <td class="label">Cidade</td>
                    <td class="field"><?=$municipioCorresp;?></td>
                </tr>
                <tr>
                    <td class="label">Bairro</td>
                    <td class="field"><?=$bairroCorresp;?></td>
                </tr>
                <tr>
                    <td class="label">CEP</td>
                    <td class="field"><?php echo formataCep($cepCorresp); ?></td>
                </tr>
                <tr>
                    <td class="label">Telefone Residencial</td>
            <td class="field"><?php echo formataFone($foneRes); ?></td>
                </tr>
                <tr>
                    <td class="label">Telefone Comercial</td>
                    <td class="field"><?php echo formataFone($foneCom); ?> - <?=$ramalCom;?></td>
                </tr>
                <tr>
                    <td class="label">Telefone Celular</td>
                    <td class="field"><?php echo formataFone($foneCel); ?></td>
                </tr>
                <tr>
                    <td class="label">e-mail</td>
                    <td class="field"><?=$email;?></td>
                </tr>
                <tr>
                    <td class="label">e-mail adicional</td>
                    <td class="field"><?=$emailAdic;?></td>
                </tr>
                <?php if ($pessoa == 'juridica') { ?>
                <tr>
                    <td class="label">CNPJ</td>
                    <td class="field"><?php echo numeroToCnpj($cnpj); ?></td>
                </tr>
                <tr>
                    <td class="label">Inscrição Estadual</td>
                    <td class="field"><?=$inscEst;?></td>
                </tr>
                <?php } ?>
                <?php if ($pessoa == 'fisica') { ?>
                <tr>
                    <td class="label">CPF</td>
                    <td class="field"><?php echo numeroToCpf($cpf); ?></td>
                </tr>
                <tr>
                    <td class="label">RG</td>
                    <td class="field"><?=$rg;?></td>
                </tr>
                <?php } ?>
                <tr>
                    <td height='5' colspan='2' class='field'>
                        &nbsp;<input type="button" value='OK' style='width: 60px;' onClick=excluirCgm();>
                        &nbsp;<input type="button" value="Cancelar"
                        onClick=document.location="excluiCgm.php?<?=Sessao::getId();?>&controle=0";>
                    </td>
                </tr>
                <tr><td height='5' colspan='2'></td></tr>
            </table>
        </form>
<?php
    }//Fim function formCgmExcluir


/***************************************************************************
Monta tela com os dados do CGA
/**************************************************************************/
    public function formCga($dadosCgm="")
    {
        if (is_array($dadosCgm)) {
            //Grava como variável o nome da chave do vetor com o seu respectivo valor
            foreach ($dadosCgm as $campo=>$valor) {
                $$campo = trim($valor);
            }
            //Carrega o cep em partes para preencher os campos segmentados
            if (isset($cep)) {
            if ($cep) {
                    $cep1 = substr($cep,0,5);
                    $cep2 = substr($cep,5,3);
                    $cep = $cep1."-".$cep2;
        }
            }
            if (isset($cepCorresp)) {
            if ($cepCorresp) {
                    $cepCorresp1 = substr($cepCorresp,0,5);
                    $cepCorresp2 = substr($cepCorresp,5,3);
                    $cepCorresp = $cepCorresp1."-".$cepCorresp2;
        }
            }

            //Carrega os telefones em partes para preencher os campos segmentados
            if (!isset($dddRes)) {
                $dddRes = substr($foneRes,0,2);
                $foneRes = substr($foneRes,2,8);
            }
            if (!isset($dddCom)) {
                $dddCom = substr($foneCom,0,2);
                $foneCom = substr($foneCom,2,8);
            }
            if (!isset($dddCel)) {
                $dddCel = substr($foneCel,0,2);
                $foneCel = substr($foneCel,2,8);
            }

            //Carrega o cpf em partes para preencher os campos segmentados
            if (isset($cpf)) {
            if ($cpf) {
                    $cpf1 = substr($cpf,0,3);
                    $cpf2 = substr($cpf,3,3);
                    $cpf3 = substr($cpf,6,3);
                    $cpf4 = substr($cpf,9,2);
                $cpf = $cpf1.".".$cpf2.".".$cpf3."-".$cpf4;
        }
            }

        if (isset($dtCadastro)) {
                 if ($dtCadastro) {
                     $arDtCadastro = explode("-",$dtCadastro);
             $dtCadastro =  $arDtCadastro[2]."/".$arDtCadastro[1]."/".$arDtCadastro[0];
         }
        }

            //Carrega o cnpj em partes para preencher os campos segmentados
            if (isset($cnpj)) {
            if ($cnpj) {
                    $cnpj1 = substr($cnpj,0,2);
                    $cnpj2 = substr($cnpj,2,3);
                    $cnpj3 = substr($cnpj,5,3);
                    $cnpj4 = substr($cnpj,8,4);
                    $cnpj5 = substr($cnpj,12,2);
                $cnpj = $cnpj1.".".$cnpj2.".".$cnpj3."/".$cnpj4."-".$cnpj5;
                }
            }
        }
?>
    <script type="text/javascript">
    <!--
    function Voltar()
    {
        document.frm.action += "&pagina=<?=$_GET['pagina'];?>&volta=true";
        document.frm.controle.value = 2;
        document.frm.submit();
    }
    //-->
    </script>
    <form name='frm' method='post' action='<?=$action;?>?<?=Sessao::getId();?>'>
    <input type="hidden" name="controle" value='<?=$controle;?>'>
    <input type="hidden" name="numCgm" value='<?=$numCgm;?>'>
    <input type="hidden" name="pessoa" value='<?=$pessoa;?>'>
    <input type="hidden" name="codUF" value='<?=$codUf;?>'>
    <input type="hidden" name="codMunicipio" value='<?=$codMunicipio;?>'>
    <input type="hidden" name="codUfCorresp" value='<?=$codUfCorresp;?>'>
    <input type="hidden" name="codMunicipioCorresp" value='<?=$codMunicipioCorresp;?>'>
    <table width="100%">
        <tr>
            <td class=alt_dados colspan=2>Dados para CGA</td>
        </tr>
        <tr>
            <td class="label" width="30%">Nome</td>
            <td class="field" width="70%">
                <?=$nomCgm;?>&nbsp;
            </td>
        </tr>
<?php if ($pessoa == 'juridica') { ?>
        <tr>
            <td class="label">CNPJ</td>
            <td class="field">
                <?=$cnpj;?>&nbsp;
            </td>
        </tr>
        <tr>
            <td class="label">Inscrição estadual</td>
            <td class="field">
                <?=$inscEst;?>&nbsp;
            </td>
        </tr>
<?php } ?>
<?php if ($pessoa == 'fisica') { ?>
        <tr>
            <td class="label">CPF</td>
            <td class="field">
                <?=$cpf;?>&nbsp;
            </td>
        </tr>
        <tr>
            <td class="label">RG</td>
            <td class="field">
                <?=$rg;?>&nbsp;
            </td>
        </tr>
        <tr>
            <td class="label">Órgão emissor</td>
            <td class="field">
                <?=$orgaoEmissor;?>&nbsp;
            </td>
        </tr>
        <tr>
            <td class="label">Data da emissão</td>
            <td class="field">
                <?=$dtEmissaoRg;?>&nbsp;
            </td>
        </tr>
        <tr>
            <td class="label">Número CNH</td>
            <td class="field">
                <?=$numCnh?>&nbsp;
            </td>
        </tr>
        <tr>
            <td class="label">Categoria de habilitação</td>
            <td class="field">
                <?=$catHabilitacao;?>&nbsp;
            </td>
        </tr>
        <tr>
            <td class="label">Data de validade da CNH</td>
            <td class="field">
                <?=$dtValidadeCnh;?>&nbsp;
            </td>
        </tr>
<?php } ?>
        <tr>
            <td colspan='2' class='alt_dados'>Dados de endereço</td></tr>
        <tr>
            <td class="label">Endereço</td>
            <td class="field">
                <?=$endereco;?>&nbsp;
            </td>
        </tr>
        <tr>
            <td class="label">Estado</td>
            <td class="field">
                <?=$estado;?>&nbsp;
            </td>
        </tr>
        <tr>
            <td class="label">Cidade</td>
            <td class="field">
                <?=$municipio;?>&nbsp;
            </td>
        </tr>
        <tr>
            <td class="label">Bairro</td>
            <td class="field">
                <?=$bairro;?>&nbsp;
            </td>
        </tr>
        <tr>
            <td class="label">CEP</td>
            <td class="field">
                <?=$cep;?>&nbsp;
            </td>
        </tr>
        <tr>
            <td colspan='2' class='alt_dados'>Dados de endereço para correspondência</td></tr>
        <tr>
            <td class="label">Endereço</td>
            <td class="field" style='margin:0'>
                <?=$enderecoCorresp;?>&nbsp;
            </td>
        </tr>
        <tr>
            <td class="label">Estado</td>
            <td class="field">
                <?=$estadoCorresp;?>&nbsp;
            </td>
        </tr>
        <tr>
            <td class="label">Cidade</td>
            <td class="field">
                <?=$municipioCorresp;?>&nbsp;
            </td>
        </tr>
        <tr>
            <td class="label">Bairro</td>
            <td class="field">
                <?=$bairroCorresp;?>&nbsp;
            </td>
        </tr>
        <tr>
            <td class="label">CEP</td>
            <td class="field">
                <?=$cepCorresp;?>&nbsp;
            </td>
        </tr>
        <tr>
            <td colspan='2' class='alt_dados'>Dados para contato</td>
        </tr>
<?php
if ($pessoa == 'fisica') {
    $stRotuloResidencial = "Telefone residencial";
    $stRotuloComercial   = "Telefone comercial";
} else {
    $stRotuloResidencial = "Telefone";
    $stRotuloComercial   = "Fax";
}
?>
        <tr>
            <td class="label"><?=$stRotuloResidencial;?></td>
            <td class="field">
                <?php echo $dddRes ? $dddRes." " : "";?>
                <?=$foneRes;?>
                <?=$ramalRes?>&nbsp;
        </td>
        </tr>
        <tr>
            <td class="label"><?=$stRotuloComercial;?></td>
            <td class="field">
                <?php echo $dddCom ? $dddCom." " : "";?>
                <?=$foneCom;?>
                <?=$ramalCom?>&nbsp;
            </td>
        </tr>
        <tr>
            <td class="label">Telefone celular</td>
            <td class="field">
                <?php $dddCel ? $dddCel." " : "";?>
                <?=$foneCel;?>&nbsp;
            </td>
        </tr>
        <tr>
            <td class="label">e-mail</td>
            <td class="field">
                <?=$email;?>&nbsp;
            </td>
        </tr>
        <tr>
            <td class="label">e-mail adicional</td>
            <td class="field">
                <?=$emailAdic;?>&nbsp;
            </td>
        </tr>
<?php
    $this->geraCamposAtributoListaCGA( $numCgm, $timestamp );
?>
    <tr>
            <td colspan='2' class='alt_dados'>Dados de cadastro</td>
        </tr>
        <tr>
            <td class="label">Data do cadastro</td>
            <td class="field">
            <?=$dtCadastro;?>&nbsp;
            </td>
        </tr>
    <tr>
            <td class="label">Responsável</td>
            <td class="field">
           <?=$nomResp;?>&nbsp;
            </td>
        </tr>
    <tr>
        <td colspan='2' class='field'>
            <input type="button" value="Voltar" onClick="JavaScript: Voltar()">
        </td>
        </tr>
    </table>
    </form>
<?php
    }//Fim function formCgm

}//Fim da classe interfaceCgm

?>
