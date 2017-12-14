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
    * Página de JavaScript de Consultar programa

    * Data de Criação   : 19/09/2008

    * @author Analista      : Bruno Ferreira
    * @author Desenvolvedor : Jânio Eduardo
    * @ignore

    * $Id:

    *Casos de uso: uc-02.09.02
*/
?>

<script type="text/javascript">

function CancelarCL()
{
<?php
    $link = Sessao::read( "link" );
    $stLink = "&pg=".$link["pg"]."&pos=".$link["pos"];
?>
    mudaTelaPrincipal("<?=$pgList.'?'.Sessao::getId().$stLink;?>");
}

//verifica se o periodo é valido
function verificaPeriodo()
{
    var dtInicio;
    var dtFinal;
    var arDtInicio = Array();
    var arDtFinal = Array();

    if (document.frm.stDataInicial.value=='') {
        alertaAviso('Campo Data inicial vazia','form','erro','<?=Sessao::getId()?>');
        document.frm.stDataInicial.focus();
    } else {
        dtInicio = document.frm.stDataInicial.value;
        arDtInicio = dtInicio.split("/");

        dtFinal = document.frm.stDataFinal.value;
        arDtFinal = dtFinal.split("/");

        if (arDtInicio[2] + arDtInicio[1] + arDtInicio[0] > arDtFinal[2] + arDtFinal[1] + arDtFinal[0]) {
          document.frm.stDataFinal.value = '';
          document.frm.stDataFinal.focus();
          alertaAviso('Campo periodo Inválido(Data Final menor que data Inicial)','form','erro','<?=Sessao::getId()?>');

        }
    }
}

function buscaValor(metodo)
{
    document.frm.stCtrl.value = metodo;
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

function incluirOrgaoLista()
{
    var stTarget   = document.frm.target;
    var stAction   = document.frm.action;

    document.frm.stCtrl.value = 'incluirOrgaoLista';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
    document.frm.stCtrl.value = 'incluirPrograma';
}

function incluirServidorLista()
{
    //alert(document.frm.stNomUsuario.value);
   montaParametrosGET( 'incluirServidorLista', '', true);
}

function excluirOrgaoLista(id)
{
    var stTarget   = document.frm.target;
    var stAction   = document.frm.action;

    document.frm.stCtrl.value = 'excluirOrgaoLista';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&id='+id;
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
    document.frm.stCtrl.value = 'incluir';
}
function excluirServidorLista(id)
{
    var stTarget   = document.frm.target;
    var stAction   = document.frm.action;

    document.frm.stCtrl.value = 'excluirServidorLista';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&id='+id;
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
    document.frm.stCtrl.value = 'incluir';
}

function excluirIndicadores(objeto)
{
    var tabela = objeto.parentNode.parentNode.parentNode;
    var linha = objeto.parentNode.parentNode;
    tabela.deleteRow(linha.rowIndex);

    if (tabela.rows.length == 2) {
        tabela.parentNode.removeChild(tabela);
        }

}

function incluirIndiceLista()
{
    var inDescricaoIndice = Array();
    var inValorIndice = Array();
    var cont;

         inValorIndice[0] = document.frm.inDescIndicador.value;
         inValorIndice[1] = document.frm.inUnidadeMedida.value;
         inValorIndice[2] = document.frm.inIndiceRecente.value;
         inValorIndice[3] = document.frm.inIndiceDesejado.value;

         inDescricaoIndice[0] = 'Descrição do Indicador';
         inDescricaoIndice[1] = 'Unidade de Medida';
         inDescricaoIndice[2] = 'Indice recente';
         inDescricaoIndice[3] = 'indice desejado no final do PPA';

         //alert(document.frm.inDescIndicador.parentNode.parentNode.childNodes[1].innerHTML);
         //alert(document.frm.inDescIndicador.parentNode.parentNode.innerHTML);

        cont = 0;
        for (var x=0;x<4;x++) {
            if (inValorIndice[x]=='') {
                alertaAviso('campo '+ inDescricaoIndice[x]+' é obrigatório para inclusao do indicador','n_incluir','aviso','<?=Sessao::getId()?>');
            cont++;
            }
        }

        if (cont == 0) {

                montaParametrosGET( 'buscaLista', '', true);

        }

}
</script>
