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
    * JavaScript de Relatório de Informações Mensais
    * Data de Criação: 26/12/2006


    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-02-14 13:57:54 -0200 (Qua, 14 Fev 2007) $

    * Casos de uso: uc-04.05.58
*/

/*
$Log$
Revision 1.2  2007/02/14 15:57:54  souzadl
alteração de caso de uso

Revision 1.1  2007/01/02 09:29:22  souzadl
construção

*/
?>

<script type="text/javascript">

function validaQuantidadeEventos(){
    obEventosSelecionados = document.frm.inCodEventoSelecionados;
    obEventosDisponiveis  = document.frm.inCodEventoDisponiveis;
    inQtnEventos = 12;
    if( obEventosSelecionados.length > inQtnEventos ){   
        ini  = obEventosSelecionados.length-1;
        arEventosDisponiveis = new Array();
        while ( ini >= inQtnEventos ){
            arEventosDisponiveis.unshift(obEventosSelecionados[ini]);
            ini--;
        }
        for(ini=0;ini<=obEventosDisponiveis.length;ini++){
            arEventosDisponiveis.unshift(obEventosDisponiveis[ini]);
        }
        if( obEventosDisponiveis.length == 0 ){
            arEventosDisponiveis.shift();
        }else{
            limpaSelect(obEventosDisponiveis,0);        
            arEventosDisponiveis.reverse();
        }
        for(ini=0;ini<=arEventosDisponiveis.length;ini++){
            obEventosDisponiveis[ini] = arEventosDisponiveis[ini];
        }
        alertaAviso('@Podem ser selecionados no máximo 12 eventos.','form','erro','<?=Sessao::getId();?>');
    }
}

function disabledQuebraPagina(){
    obQuebrarPagina = document.frm.boQuebrarPagina;
    if( obQuebrarPagina.disabled == true ){
        obQuebrarPagina.disabled = false;
    }else{
        obQuebrarPagina.disabled = true;
    }
}

</script>
