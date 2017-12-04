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
* Arquivo de instância para Relatórios
* Data de Criação: 27/02/2003

* @author Analista:
* @author Desenvolvedor: Ricardo Lopes de Alencar

* @package URBEM
* @subpackage

$Revision: 3219 $
$Name$
$Author: lizandro $
$Date: 2005-12-01 14:25:34 -0200 (Qui, 01 Dez 2005) $

* Casos de uso: uc-01.07.99
*/

  include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
  include_once (CAM_FW_LEGADO."funcoesLegado.lib.php"       );
  include_once (CAM_FW_LEGADO."paginacaoLegada.class.php"   );
  include_once (CAM_FW_LEGADO."botoesPdfLegado.class.php"   );

if (isset($pagina)) {
    $controle = 1;
}

if (!isset($controle)) {
    $controle = 0;
    $sessao->transf = "";
    $tipoBusca = "geral";
}

switch ($controle) {
//Formulário em HTML para entrada de dados
case 0:
?>
<script type="text/javascript">
    function validacao(cod)
    {
        var f = document.frm;
        f.target = 'oculto';
        f.controle.value = cod;
        f.submit();
    }

    function procurarCidadao(nomeform,campocodcidadao,camponomcidadao,sessao)
    {
       var x = 350;
       var y = 200;
       var sessaoid = sessao.substr(10,6);
       var sArq = '../../includes/procuraCidadao.php?'+sessao+'&nomForm='+nomeform+'&campoCodCidadao='+campocodcidadao+'&campoNomCidadao='+camponomcidadao;
       var wVolta=false;
       var sAux = "prcidadao"+ sessaoid +" = window.open(sArq,'prcidadao"+ sessaoid +"','width=800px,height=550px,resizable=1,scrollbars=1,left="+x+",top="+y+"');";
       eval(sAux);
    }

    function Valida()
    {
        var mensagem = "";
        var erro = false;
        var campo;
        var campoaux;
        var f = document.frm;

        if (erro) alertaAviso(mensagem,'form','erro','<?=$sessao->id;?>');
        return !(erro);
    }// Fim da function Valida

    //A função salvar testa a validação, e se tudo ocorrer certo, envia o form
    function Salvar()
    {
        var f = document.frm;
        f.ok.disabled = true;
        if (Valida()) {
            f.controle.value = 1;
            f.target = "telaPrincipal";
            f.action = "<?=$PHP_SELF;?>?<?=$sessao->id;?>&filtro=1";
            f.submit();
        } else {
            f.ok.disabled = false;
        }
    }

    function buscaCidadao()
    {
        var f = document.frm;
        f.target = 'oculto';
        f.controle.value = 4;
        f.submit();
    }
</script>
<form name='frm' method='post' action='<?=$PHP_SELF;?>?<?=$sessao->id;?>' target='telaPrincipal'>
<input type='hidden' name='controle' value='1'>
<table width='100%'>
<tr><td class="alt_dados" colspan="2">Filtrar por</td></tr>
<tr>
    <td class=label width="20%">Cidadão</td>
    <td class=field width="80%">
        <input type='text' name='codCidadao' value="<?=$codCidadao;?>" size='5' maxlength='10' onKeyUp="return autoTab(this, 10, event);" onKeyPress="return(isValido(this, event, '0123456789'))" onChange="buscaCidadao();">
        <input type='text' name='nomCidadao' value="<?=$nomCidadao;?>" size='60' maxlength='200' readonly="" tabindex="1">
        <a href='javascript:abrePopUp("<?=CAM_GA_CSE_POPUPS."Cidadao/procuraCidadao.php";?>","frm","codCidadao","nomCidadao","","<?=$sessao->id?>","800","550");'>
            <img src="<?=CAM_FW_IMAGENS."procuracgm.gif";?>" alt="Procurar cidadão" width=22 height=22 border=0>
        </a>
    </td>
</tr>
<tr>
    <td class=label width="20%">Domicílio</td>
    <td class=field width="80%">
        <input type='text' name='codDomicilio' value="" size='5' maxlength='10' onKeyUp="return autoTab(this, 10, event);" onKeyPress="return(isValido(this, event, '0123456789'))" onBlur="validacao(3);">
        <input type="text" name="logradouro" value="" size=30 maxlength=60 readonly='' tabindex='1'>
        <a href="javascript:procuraDomicilio('frm','codDomicilio','logradouro','<?=$sessao->id?>');" tabindex='1'>
        <img title="Procurar Domicílio" src="<?=CAM_FW_IMAGENS."procuracgm.gif";?>" width=20 height=20 border=0>
        </a>
    </td>
</tr>
<tr>
    <td class='label' width='20%'>Bairro</td>
    <td class='field' width='80%'>
        <input type='text' name='bairro' value="" size='40' maxlength='60' >
    </td>
</tr>
<tr>
    <td class='label' width='20%'>Classificação de Tratamento</td>
    <td class='field' width='80%'>
        <?php
            $combo = montaComboGenerico("codClassificacao", "cse.classificacao_tratamento", "cod_classificacao", "nom_classificacao", $codClassificacao,
                     "style='width: 200px;' onchange='validacao(2);' ",
                     "", true, false, false);
            echo $combo;
        ?>
    </td>
</tr>
<tr>
    <td class='label' width='20%'>Tipo de Tratamento</td>
    <td class='field' width='80%'>
        <select name='codTipo' style='width: 200px;' disabled>
            <option value='XXX' selected>Selecione</option>
        </select>
    </td>
</tr>
<tr>
    <td class='label' width='20%'>Instituição de Saúde (Internação)</td>
    <td class='field' width='80%'>
        <?php
            $combo = montaComboGenerico("codInstituicao", "cse.instituicao_saude", "cod_instituicao", "nom_instituicao", $codInstituicao,
                     "style='width: 200px;' ", "", true, false, false);
            echo $combo;
        ?>
    </td>
</tr>
<tr>
    <td class='label' width='20%' title="Há gestantes no domicílio ?">Gestante no Domicílio</td>
    <td class='field' width='80%'>
        <select name='gestante' style='width: 100px;' >
            <option value='XXX' selected>Selecione</option>
            <option value='1'>Sim</option>
            <option value='0'>Não</option>
        </select>
    </td>
</tr>
<tr>
    <td class='label' width='20%' >Quantidade de Filhos</td>
    <td class='field' width='80%'>
        <select name='operadorFilhos' style='width: 150px;' >
            <option value='=' selected>igual a</option>
            <option value='>='>maior ou igual a </option>
            <option value='<='>menor ou igual a </option>
        </select>
        <input type='text' name='qtdFilhos' value="" size='3' maxlength='3' onKeyUp="return autoTab(this, 3, event);" onKeyPress="return(isValido(this, event, '0123456789'))" >
    </td>
</tr>
<tr>
    <td class='label' width='20%'>Qualificação Profissional</td>
    <td class='field' width='80%'>
        <?php
            $combo = montaComboGenerico("codProfissao", "cse.profissao", "cod_profissao", "nom_profissao", "",
                     "style='width: 200px;' ", "", true, false, false);
            echo $combo;
        ?>
    </td>
</tr>
<tr>
    <td colspan='2' class='field'>
        <?php geraBotaoOk(); ?>
    </td>
</tr>
</table>
</form>
<?php
break;

//Gera o relatório em html e pdf
case 1:

//Se estiver vindo do formulário gera o filtro
if (isset($filtro)) {
    $sql = " ";
    $flagTratamento = false;
    $sub = "";

    //Gera um filtro para cidadãos que moram em um determinado domicílio ou bairro
    if (strlen($codCidadao) > 0) {
        $sql .= "And cod_cidadao = ".$codCidadao." ";
    }

    if (strlen($codDomicilio) > 0) {
        $sql .= "And cod_cidadao IN (
                    Select cod_cidadao From cse.vw_ultimo_domicilio_cidadao
                    Where cod_domicilio = '".$codDomicilio."'
                ) ";
    }
    if (strlen($bairro) > 0) {
        $sql .= "And cod_cidadao IN (
                    Select ud.cod_cidadao
                    From cse.vw_ultimo_domicilio_cidadao as ud, cse.domicilio as d
                    Where ud.cod_domicilio = d.cod_domicilio
                    And lower(d.bairro) LIKE lower('%".$bairro."%')
                ) ";
    }

    //Gera o filtro para cidadãos que fizeram um determinado tipo de tratamento
    if (isset($codClassificacao) and $codClassificacao != 'XXX') {
        $flagTratamento = true;
        $sub .= " And et.cod_classificacao = '".$codClassificacao."' ";
    }
    if (isset($codTipo) and $codTipo != 'XXX') {
        $flagTratamento = true;
        $sub .= " And et.cod_tipo = '".$codTipo."' ";
    }

if ($flagTratamento) {
        $sql .= "And cod_cidadao IN (
                    Select c.cod_cidadao
                    From cse.prescricao_exame as et, cse.cidadao as c
                    Where et.cod_cidadao = c.cod_cidadao
                    ".$sub."
                ) ";
    }
    //Gera o filtro para cidadãos que foram internados em uma determinada instituição

    if (isset($codInstituicao) and $codInstituicao != 'XXX') {
        $sql .= "And cod_cidadao IN (
                    Select c.cod_cidadao
                    From cse.prescricao_internacao as i, cse.cidadao as c
                    Where i.cod_cidadao = c.cod_cidadao
                    And i.cod_instituicao = '".$codInstituicao."'
                ) ";
    }

    //Gera o filtro de acordo com a quantidade de filhos
    if (strlen($qtdFilhos) > 0) {
        $sql .= " And qtd_filhos ".$operadorFilhos." '".$qtdFilhos."' ";
    }

    //Gera o filtro de acordo com a profissão do cidadão
    if (isset($codProfissao) and $codProfissao != 'XXX') {
        $sql .= "And cod_cidadao IN (
                    Select cod_cidadao From cse.vw_qualificacao_profissional
                    Where cod_profissao = '".$codProfissao."'
                ) ";
    }

    //Gera o filtro que verifica se há ou não gestantes em um determinado domicílio
    if (isset($gestante) and $gestante != 'XXX') {
        $sql .= "And cod_cidadao IN (
                    Select ud.cod_cidadao
                    From cse.vw_ultimo_domicilio_cidadao as ud, cse.domicilio as d
                    Where ud.cod_domicilio = d.cod_domicilio ";
            if ($gestante) {
                $sql .= " And d.qtd_gravidas > 0 ";
            } else {
                $sql .= " And d.qtd_gravidas = 0 ";
            }
        $sql .= ") ";
    }

    //Grava a query em uma variável de sessão
    $sessao->transf[sql] = "Select cod_cidadao, nom_cidadao From cse.cidadao
                            Where cod_cidadao IN (
                                Select cod_cidadao From cse.vw_cidadao_programa
                            ) ".$sql;

    //Query que retorna o número total de cidadão que se encaixam dentro dos parâmentros selecionados pelos filtros.
    $sessao->transf[total] = "Select count(cod_cidadao) as total From (".$sessao->transf[sql].") as tot ";
    //echo $sessao->transf[total];
    //echo $sessao->transf[sql];

}
//Inicia o relatório em html
$paginacao = new paginacaoLegada;
$paginacao->pegaDados($sessao->transf[sql],"15");
$paginacao->pegaPagina($pagina);
$paginacao->geraLinks();
$paginacao->pegaOrder("lower(nom_cidadao)","ASC");
$sSQL = $paginacao->geraSQL();

//Pega os dados encontrados em uma query
$conn = new dataBaseLegado;
$conn->abreBD();
$conn->abreSelecao($sSQL);
$conn->fechaBD();
$conn->vaiPrimeiro();

if($conn->numeroDeLinhas==0)
    exit("<br><b>Nenhum registro encontrado!</b>");

$html = "<table width='95%'>";

while (!$conn->eof()) {
    $codCidadao = $conn->pegaCampo("cod_cidadao");
    $nomCidadao = $conn->pegaCampo("nom_cidadao");
    $conn->vaiProximo();
    $html .= "
        <tr>
            <td class='alt_dados' width='20%' nowrap>Código Cidadão</td>
            <td class='alt_dados' width='80%' >Nome Cidadão</td>
        </tr>";
    $html .= "
        <tr>
            <td class='show_dados'>".$codCidadao."</td>
            <td class='show_dados'>".$nomCidadao."</td>
        </tr>";

    $sql2 = "Select vw.nom_programa, cp.dt_inclusao, cp.vl_beneficio, cp.prioritario
            From cse.vw_cidadao_programa as vw, cse.cidadao_programa as cp
            Where vw.cod_cidadao = '".$codCidadao."'
            And vw.cod_programa = cp.cod_programa
            And vw.exercicio = cp.exercicio
            And vw.cod_cidadao = cp.cod_cidadao
            Order by lower(vw.nom_programa) ASC ";

    $conn2 = new dataBaseLegado;
    $conn2->abreBD();
    $conn2->abreSelecao($sql2);
    $conn2->fechaBD();
    $conn2->vaiPrimeiro();
    if ($conn2->numeroDeLinhas > 0) {
        $html .= "
    <tr>
    <td colspan='2'>
        <table width='100%'>
        <tr>
            <td class='labelleft' width='50%' nowrap>Programas do Cidadão</td>
            <td class='labelleft' width='20%' nowrap>Data Inclusão</td>
            <td class='labelleft' width='20%' nowrap>Valor Benefício</td>
            <td class='labelleft' width='10%'>Prioritário</td>
        </tr>";
    }
    while (!$conn2->eof()) {
        $nomPrograma = $conn2->pegaCampo("nom_programa");
        $data = dataToBr($conn2->pegaCampo("dt_inclusao"));
        $valor = $conn2->pegaCampo("vl_beneficio");
        $valor = "R$ ".number_format($valor, 2, ',', '.');
        $prioritario = $conn2->pegaCampo("prioritario");
        if ($prioritario != "f") {
            $prioritario = "Sim";
        } else {
            $prioritario = "Não";
        }
        $conn2->vaiProximo();
        $html .= "
        <tr>
            <td class='show_dados'>".$nomPrograma."</td>
            <td class='show_dados'>".$data."</td>
            <td class='show_dados_right' nowrap>".$valor."</td>
            <td class='show_dados'>".$prioritario."</td>
        </tr>";
    }
    $conn2->limpaSelecao();

    if ($conn2->numeroDeLinhas > 0) {
        $html .= "</table></td></tr>";
    }

    //Espaço entre registros
    $html .= "<tr><td colspan='2' height='3'></td></tr>";

}
$conn->limpaSelecao();
$html .= "</table><hr width='85%'>";

//Gera o totalizador
$html .= "<table width='85%'>
          <tr>
              <td class='alt_dados' colspan='2' >Totalizador</td>
          </tr>
          <tr>
              <td class='labelleft' width='80%'>Nome do Programa</td>
              <td class='labelleft' width='20%' nowrap>% Cidadãos Atendidos</td>
          </tr>";

$sql = "Select ps.cod_programa, ps.nom_programa,
        CAST (
        ( CAST ((Select count(cp.cod_cidadao)
                 From cse.vw_cidadao_programa as cp
                 Where cp.cod_programa = ps.cod_programa
                 And cp.cod_cidadao IN (Select cod_cidadao From (".$sessao->transf[sql].") as cid)
                 ) as numeric(8,2)  )
        / CAST((Select count(cod_cidadao) as total From (".$sessao->transf[sql].") as tot) as numeric(8,2) ) * 100 )
        as numeric(8,2) ) as total_cidadaos
        From cse.programa_social as ps
        Order by lower(ps.nom_programa); ";
//Pega os dados encontrados em uma query
$conn = new dataBaseLegado;
$conn->abreBD();
$conn->abreSelecao($sql);
$conn->fechaBD();
$conn->vaiPrimeiro();
    while (!$conn->eof()) {
        $nomPrograma = $conn->pegaCampo("nom_programa");
        $total = $conn->pegaCampo("total_cidadaos");
        $total = number_format($total, 2, ',', '.');
        $conn->vaiProximo();
        $html .= "
        <tr>
            <td class='show_dados' width='80%'>".$nomPrograma."</td>
            <td class='show_dados' width='20%'>".$total." %</td>
        </tr>";
    }
$conn->limpaSelecao();

$html .= "</table>";

    //Mostra a opção de imprimir ou salvar o relatório
    $pdf = $sessao->transf[sql]." Order by lower(nom_cidadao) ASC; ";
    $pdf .= "Select vw.nom_programa, cp.dt_inclusao, cp.vl_beneficio, cp.prioritario
            From cse.vw_cidadao_programa as vw, cse.cidadao_programa as cp
            Where vw.cod_cidadao = &cod_cidadao
            And vw.cod_programa = cp.cod_programa
            And vw.exercicio = cp.exercicio
            And vw.cod_cidadao = cp.cod_cidadao
            Order by lower(vw.nom_programa) ASC; ";
    $pdf .= $sql; //Anexa a query do totalizador
    $sXML       = CAM_CSE.'cse/relatorios/relatorioCidadao.xml';
    $botoesPDF  = new botoesPdfLegado;
    $botoesPDF->imprimeBotoes($sXML,$pdf,'','');

echo $html;

?>
    <table width='450' align='center'><tr><td align='center'><font size='2'>
    <?php $paginacao->mostraLinks();  ?>
    </font></tr></td></table>
<?php
    break;

//Gera as opções de tipo de tratamento de acordo com a classificação escolhida
case 2:
    $js = "";
    $js .= "campo = f.codTipo; \n";
    $js .= "campo.disabled = false; \n";
    $js .= "limpaSelect(f.codTipo,1); \n";
    if ($codClassificacao != "XXX" or $codClassificacao > 0) {
        $js .= "campo.disabled = false;";
        $sql = "Select cod_tratamento, nom_tratamento
            From cse.tipo_tratamento
            Where cod_classificacao = ".$codClassificacao;
        //Pega os dados encontrados em uma query
        $conn = new dataBaseLegado;
        $conn->abreBD();
        $conn->abreSelecao($sql);
        $conn->fechaBD();
        $conn->vaiPrimeiro();
        $cont = 1;
            while (!$conn->eof()) {
                $cod = $conn->pegaCampo("cod_tratamento");
                $nom = $conn->pegaCampo("nom_tratamento");
                $js .= 'campo.options['.$cont.'] = new Option("'.$nom.'",'.$cod.'); ';
                $conn->vaiProximo();
                $cont++;
            }
        $conn->limpaSelecao();
    } else {
        $js .= "campo.disabled = true;";
    }
    break;

case 3:
    $js = "";
    if (strlen($codDomicilio) > 0) {
        $sql = "Select (t.nom_tipo || ' ' || d.logradouro || ' ' || d.numero || ' ' || d.complemento) as logr
                From cse.domicilio as d, sw_tipo_logradouro as t
                Where d.cod_domicilio = '".$codDomicilio."'
                And d.cod_tipo_logradouro = t.cod_tipo ";
        //Pega os dados encontrados em uma query
        $conn = new dataBaseLegado;
        $conn->abreBD();
        $conn->abreSelecao($sql);
        $conn->fechaBD();
        $conn->vaiPrimeiro();
            if (!$conn->eof()) {
                $logradouro = $conn->pegaCampo("logr");
                $conn->vaiProximo();
            } else {
                $logradouro = "Domicílio Inexistente";
            }
        $conn->limpaSelecao();
    } else {
        $logradouro = "";
    }
    $js .= "f.logradouro.value = '".$logradouro."'; \n";
    break;
case 4:
    $js = "f.controle.value = 0; \n";
    if ($codCidadao > 0) {
        if (!$nomCidadao = pegaDado("nom_cidadao","cse.cidadao","Where cod_cidadao = '".$codCidadao."' ")) {
            $js = "alertaAviso('Cidadão inválido!(".$codCidadao.")','form','erro','".$sessao->id."')\n";
            $nomCidadao = "";
        }
    } else {
        $nomCidadao = "";
    }
    $js .= 'f.nomCidadao.value = "'.$nomCidadao.'" ';

    break;
}//fim switch

executaFrameOculto($js);

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php'; //Insere o fim da página html
?>
