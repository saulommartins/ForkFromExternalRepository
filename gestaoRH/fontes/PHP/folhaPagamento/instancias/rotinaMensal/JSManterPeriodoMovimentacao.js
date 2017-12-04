<script type="text/javascript">
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
</script>
<?php
/**
    * Página de JavaScript do ManterPeriodoMovimentacao
    * Data de Criação   : 26/10/2005


    * @author Analista: Vandre Miguel Ramos
    * @author Desenvolvedor: Andre Almeida
    
    * @ignore

    $Revision: 30566 $
    $Name$
    $Author: andre $
    $Date: 2007-05-09 12:01:52 -0300 (Qua, 09 Mai 2007) $

    * Caso de uso: uc-04.05.40

*/

/*
$Log$
Revision 1.4  2007/05/09 15:01:22  andre
Bug #9213#

Revision 1.3  2006/08/08 17:43:42  vandre
Adicionada tag log.

*/

?>
<script type="text/javascript">

    //formata a data do formato dd/mm/aaaa para mm/dd/aaaa
    function formataData( str ) {
        var Dia = str.substr(0, 2);
        var Mes = str.substr(3, 2);
        var Ano = str.substr(6, 4);
        dataFormatada = Mes + "/"+ Dia + "/" + Ano;
        return dataFormatada;
    }


    //valida se a data inicial é menor que a data final.
    function validaData( stDataInicial ) {
        dataInicial = new Date( formataData(stDataInicial) );
        dataFinal   = new Date( formataData(document.frm.stNovaDataFinal.value)   );
        if (dataInicial >= dataFinal) {
            alertaAviso("@A data final deve ser posterior a data inicial.","form","erro","<?=Sessao::getId();?>");
        }
        
    }


function buscaValorFiltro(tipoBusca){
     target = document.frm.target ;
     action = document.frm.action;
     document.frm.stCtrl.value = tipoBusca;
     document.frm.target = 'oculto';
     document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>'
     document.frm.submit();
     document.frm.action = action;
     document.frm.target = target;
}    
</script>
