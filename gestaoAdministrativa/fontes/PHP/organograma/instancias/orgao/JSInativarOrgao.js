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
<?
/**
* Arquivo de instância para manutenção de orgao
* Data de Criação: 25/07/2005


* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 12498 $
$Name$
$Author: cassiano $
$Date: 2006-07-11 16:59:06 -0300 (Ter, 11 Jul 2006) $

Casos de uso: uc-01.05.02
*/
?>

<script type="text/javascript">
function VoltarTP(){
    document.frm.target = 'telaPrincipal';
    document.frm.action = 'LSInativarOrgao.php?<?=Sessao::getId();?>';
    document.frm.submit();
}

function ValidaData( DataIni, DataFim ){
    var DataIniTmp = DataIni.substr(6,4) + DataIni.substr(3,2) + DataIni.substr(0,2)
    var DataFimTmp = DataFim.substr(6,4) + DataFim.substr(3,2) + DataFim.substr(0,2)
    if ( parseInt( DataFimTmp ) > parseInt( DataIniTmp ) ){
        return true
    }else{
        return false
    } 
}
</script>
