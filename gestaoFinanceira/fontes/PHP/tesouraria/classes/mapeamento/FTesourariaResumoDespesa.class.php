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
    * Classe de mapeamento da tabela FN_RELATORIO_RESUMO_DESPESA
    * Data de Criação: 05/12/2005

    * @author Analista: Lucas Leusin Oiagem
    * @author Desenvolvedor: Jose Eduardo Porto

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 31732 $
    $Name$
    $Autor: $
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.04.16
*/

/*
$Log$
Revision 1.10  2006/12/05 21:10:38  cleisson
Bug #7551#

Revision 1.9  2006/07/05 20:38:37  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FTesourariaResumoDespesa extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FTesourariaResumoDespesa()
{
    parent::Persistente();
}

function montaRecuperaTodos()
{
    $stSql  = "select *                                                                                       \n";
    $stSql .= "    from tesouraria.fn_relatorio_resumo_despesa('".$this->getDado("stEntidade") ."',           \n";
    $stSql .= "    '" . $this->getDado("stExercicio")         . "',                                           \n";
    $stSql .= "    '" . $this->getDado("stDataInicial")       . "',                                           \n";
    $stSql .= "    '" . $this->getDado("stDataFinal")         . "',                                           \n";
    $stSql .= "    '" . $this->getDado("stTipoRelatorio")     . "',                                           \n";
    $stSql .= "     " . $this->getDado("inDespesaInicial")    . " ,                                           \n";
    $stSql .= "     " . $this->getDado("inDespesaFinal")      . " ,                                           \n";
    $stSql .= "     " . $this->getDado("inContaBancoInicial") . " ,                                           \n";
    $stSql .= "     " . $this->getDado("inContaBancoFinal")   . " ,                                           \n";
    $stSql .= "     " . $this->getDado("inCodRecurso")        . " ,                                           \n";
    $stSql .= "     '". $this->getDado("stDestinacaoRecurso") . "',                                           \n";
    $stSql .= "     '". $this->getDado("inCodDetalhamento")   . "',                                           \n";
    $stSql .= "     '". $this->getDado("boUtilizaEstruturalTCE")   . "'                                       \n";
    $stSql .= "    ) as retorno (  despesa              integer,            \n";
    $stSql .= "                    descricao            varchar,                                                              \n";
    $stSql .= "                    pago                 numeric,                                                              \n";
    $stSql .= "                    estornado            numeric,                                                              \n";
    $stSql .= "                    tipo_despesa         varchar,                                                              \n";
    $stSql .= "                    complemento          varchar)                                                              \n";

    return $stSql;

}

function recuperaBoletimDespesa(&$rsRecordSet, $stCondicao = "", $stOrder = "",  $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if ($stOrder != "") {
        if( !strstr( $stOrder, "ORDER BY" ) )
            $stOrder = " ORDER BY ".$stOrder;
    }
    $stSql = $this->montaRecuperaBoletimDespesa().$stCondicao;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaBoletimDespesa()
{
    $stSql  = "select *                                                                                       \n";
    $stSql .= "    from tesouraria.fn_boletim_despesa         ('".$this->getDado("stEntidade") ."',           \n";
    $stSql .= "    '" . $this->getDado("stExercicio")         . "',                                           \n";
    $stSql .= "    '" . $this->getDado("stDataInicial")       . "',                                           \n";
    $stSql .= "    '" . $this->getDado("stDataFinal")         . "',                                           \n";
    $stSql .= "     " . $this->getDado("inNumCgm")            . " ) as retorno(minimo     integer,            \n";
    $stSql .= "    maximo         integer)                                                                      ";

    return $stSql;

}

}
