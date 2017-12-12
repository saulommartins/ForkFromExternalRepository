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
    * Classe de mapeamento da tabela FN_RELATORIO_TRANSFERENCIAS_BANCARIAS
    * Data de Criação: 30/11/2005

    * @author Analista: Lucas Leusin Oiagem
    * @author Desenvolvedor: Jose Eduardo Porto

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

	$Id: FTesourariaTransferenciasBancarias.class.php 60879 2014-11-20 13:53:10Z michel $

    * Casos de uso: uc-02.04.16
*/

/*
$Log$
Revision 1.7  2006/07/05 20:38:37  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FTesourariaTransferenciasBancarias extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FTesourariaTransferenciasBancarias()
{
    parent::Persistente();
}

function montaRecuperaTodos()
{
    $stSql  = "select retorno.* , TT.cod_tipo||' - '||TT.descricao AS tipo_transferencia_to                   \n";
    $stSql .= "    from tesouraria.fn_relatorio_transferencias_bancarias('".$this->getDado("stExercicio")."', \n";
    $stSql .= "    '" . $this->getDado("stEntidade")            . "',                                         \n";
    $stSql .= "    '" . $this->getDado("stDataInicial")         . "',                                         \n";
    $stSql .= "    '" . $this->getDado("stDataFinal")           . "',                                         \n";
    $stSql .= "     " . $this->getDado("inContaBancoInicial")   . " ,                                         \n";
    $stSql .= "     " . $this->getDado("inContaBancoFinal")     . " ,                                         \n";
    $stSql .= "     " . $this->getDado("inCodTipoTransferencia"). " ,                                         \n";
    $stSql .= "    '" . $this->getDado("boUtilizaEstruturalTCE"). "') as retorno(data       varchar,          \n";
    $stSql .= "    lote                 varchar,                                                              \n";
    $stSql .= "    credito              varchar,                                                              \n";
    $stSql .= "    debito               varchar,                                                              \n";
    $stSql .= "    valor                numeric,                                                              \n";
    $stSql .= "    tipo                 integer)                                                              \n";
    $stSql .= "    left join tceto.transferencia_tipo_transferencia AS TTT                                    \n";
    $stSql .= "      on TTT.cod_entidade=".$this->getDado("stEntidade")."                                     \n";
    $stSql .= "     and TTT.tipo='T'                                                                          \n";
    $stSql .= "     and TTT.cod_lote||'/'||TTT.exercicio=retorno.lote                                         \n";
    $stSql .= "    left join tceto.tipo_transferencia AS TT                                                   \n";
    $stSql .= "      on TT.cod_tipo=TTT.cod_tipo_transferencia                                                \n";

    return $stSql;

}

}
