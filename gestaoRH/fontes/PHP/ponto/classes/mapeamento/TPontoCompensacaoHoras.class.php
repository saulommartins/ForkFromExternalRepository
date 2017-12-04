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
    * Classe de mapeamento da tabela ponto.compensacao_horas
    * Data de Criação: 03/10/2008

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPontoCompensacaoHoras extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPontoCompensacaoHoras()
{
    parent::Persistente();
    $this->setTabela("ponto.compensacao_horas");

    $this->setCampoCod('cod_compensacao');
    $this->setComplementoChave('cod_contrato');

    $this->AddCampo('cod_compensacao','sequence',true  ,'',true,false);
    $this->AddCampo('cod_contrato'   ,'integer' ,true  ,'',true,'TPessoalContrato');
    $this->AddCampo('dt_falta'       ,'date'    ,true  ,'',false,false);
    $this->AddCampo('dt_compensacao' ,'date'    ,true  ,'',false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql .= "    SELECT cadastro.registro\n";
    $stSql .= "         , cadastro.cod_contrato as codigo\n";
    $stSql .= "         , cadastro.nom_cgm\n";
    $stSql .= "         , cadastro.registro||'-'||cadastro.nom_cgm as descricao\n";
    $stSql .= "         , to_char(compensacao_horas.dt_falta,'dd/mm/yyyy') as dt_falta\n";
    $stSql .= "         , to_char(compensacao_horas.dt_compensacao,'dd/mm/yyyy') as dt_compensacao\n";
    $stSql .= "      FROM ponto.compensacao_horas\n";
    $stSql .= "INNER JOIN (SELECT *\n";
    $stSql .= "              FROM recuperarContratoServidor('cgm','".Sessao::getEntidade()."',0,'".$this->getDado('stTipoFiltro')."','".$this->getDado('stCodigos')."','".$this->getDado("exercicio")."')\n";
    $stSql .= "           ) as cadastro\n";
    $stSql .= "        ON compensacao_horas.cod_contrato = cadastro.cod_contrato\n";
    $stSql .= "     WHERE NOT EXISTS (SELECT 1\n";
    $stSql .= "                         FROM ponto.compensacao_horas_exclusao\n";
    $stSql .= "                        WHERE compensacao_horas_exclusao.cod_compensacao = compensacao_horas.cod_compensacao\n";
    $stSql .= "                          AND compensacao_horas_exclusao.cod_contrato = compensacao_horas.cod_contrato)\n";
    $stSql .= "       AND recuperarSituacaoDoContrato(cadastro.cod_contrato,0,'".Sessao::getEntidade()."') NOT IN ('R')\n";

    return $stSql;
}

function recuperaCompesacoesLotacao(&$rsRecordset,$stFiltro="",$stOrdem="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaCompesacoesLotacao",$rsRecordset,$stFiltro,$stOrdem,$boTransacao);
}

function montaRecuperaCompesacoesLotacao()
{
    $stSql .= "    SELECT cadastro.desc_orgao as descricao\n";
    $stSql .= "         , cadastro.cod_orgao as codigo\n";
    $stSql .= "         , to_char(compensacao_horas.dt_falta,'dd/mm/yyyy') as dt_falta\n";
    $stSql .= "         , to_char(compensacao_horas.dt_compensacao,'dd/mm/yyyy') as dt_compensacao\n";
    $stSql .= "      FROM ponto.compensacao_horas\n";
    $stSql .= "INNER JOIN (SELECT *\n";
    $stSql .= "              FROM recuperarContratoServidor('oo,o','".Sessao::getEntidade()."',0,'".$this->getDado('stTipoFiltro')."','".$this->getDado('stCodigos')."','".$this->getDado("exercicio")."')\n";
    $stSql .= "           ) as cadastro\n";
    $stSql .= "        ON compensacao_horas.cod_contrato = cadastro.cod_contrato\n";
    $stSql .= "     WHERE NOT EXISTS (SELECT 1\n";
    $stSql .= "                        FROM ponto.compensacao_horas_exclusao\n";
    $stSql .= "                       WHERE compensacao_horas_exclusao.cod_compensacao = compensacao_horas.cod_compensacao\n";
    $stSql .= "                         AND compensacao_horas_exclusao.cod_contrato = compensacao_horas.cod_contrato)\n";
    $stSql .= "       AND recuperarSituacaoDoContrato(cadastro.cod_contrato,0,'".Sessao::getEntidade()."') NOT IN ('R')\n";

    if (trim($this->getDado("dt_falta_inicial")) != ""  and trim($this->getDado("dt_falta_final")) != "") {
        $stSql .= "    AND compensacao_horas.dt_falta BETWEEN '".trim($this->getDado("dt_falta_inicial"))."'\n";
        $stSql .= "                                       AND '".trim($this->getDado("dt_falta_final"))."'\n";
    }
    if (trim($this->getDado("dt_falta_inicial")) != ""  and trim($this->getDado("dt_falta_final")) == "") {
        $stSql .= "    AND compensacao_horas.dt_falta = '".trim($this->getDado("dt_falta_inicial"))."'\n";
    }
    if (trim($this->getDado("dt_falta_inicial")) == ""  and trim($this->getDado("dt_falta_final")) != "") {
        $stSql .= "    AND compensacao_horas.dt_falta = '".trim($this->getDado("dt_falta_final"))."'\n";
    }
    if (trim($this->getDado("dt_compensacao_inicial")) != "" and trim($this->getDado("dt_compensacao_final")) != "") {
        $stSql .= "    AND compensacao_horas.dt_compensacao BETWEEN '".trim($this->getDado("dt_compensacao_inicial"))."'\n";
        $stSql .= "                                            AND  '".trim($this->getDado("dt_compensacao_final"))."'\n";
    }
    if (trim($this->getDado("dt_compensacao_inicial")) != "" and trim($this->getDado("dt_compensacao_final")) == "") {
        $stSql .= "    AND compensacao_horas.dt_compensacao = '".trim($this->getDado("dt_compensacao_inicial"))."'\n";
    }
    if (trim($this->getDado("dt_compensacao_inicial")) == "" and trim($this->getDado("dt_compensacao_final")) != "") {
        $stSql .= "    AND compensacao_horas.dt_compensacao = '".trim($this->getDado("dt_compensacao_final"))."'\n";
    }
    $stSql .= "GROUP BY  cadastro.cod_orgao\n";
    $stSql .= "        , cadastro.desc_orgao\n";
    $stSql .= "        , compensacao_horas.dt_falta\n";
    $stSql .= "        , compensacao_horas.dt_compensacao\n";

    return $stSql;
}

function recuperaCompesacoesLocal(&$rsRecordset,$stFiltro="",$stOrdem="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaCompesacoesLocal",$rsRecordset,$stFiltro,$stOrdem,$boTransacao);
}

function montaRecuperaCompesacoesLocal()
{
    $stSql .= "   SELECT cadastro.cod_local||'-'||cadastro.desc_local as descricao\n";
    $stSql .= "        , cadastro.cod_local as codigo\n";
    $stSql .= "        , to_char(compensacao_horas.dt_falta,'dd/mm/yyyy') as dt_falta\n";
    $stSql .= "        , to_char(compensacao_horas.dt_compensacao,'dd/mm/yyyy') as dt_compensacao\n";
    $stSql .= "      FROM ponto.compensacao_horas\n";
    $stSql .= "INNER JOIN (SELECT *\n";
    $stSql .= "              FROM recuperarContratoServidor('l','".Sessao::getEntidade()."',0,'".$this->getDado('stTipoFiltro')."','".$this->getDado('stCodigos')."','".$this->getDado("exercicio")."')\n";
    $stSql .= "           ) as cadastro\n";
    $stSql .= "        ON compensacao_horas.cod_contrato = cadastro.cod_contrato\n";
    $stSql .= "     WHERE NOT EXISTS (SELECT 1\n";
    $stSql .= "                        FROM ponto.compensacao_horas_exclusao\n";
    $stSql .= "                       WHERE compensacao_horas_exclusao.cod_compensacao = compensacao_horas.cod_compensacao\n";
    $stSql .= "                         AND compensacao_horas_exclusao.cod_contrato = compensacao_horas.cod_contrato)\n";
    $stSql .= "       AND recuperarSituacaoDoContrato(cadastro.cod_contrato,0,'".Sessao::getEntidade()."') NOT IN ('R')\n";

    if (trim($this->getDado("dt_falta_inicial")) != ""  and trim($this->getDado("dt_falta_final")) != "") {
        $stSql .= "    AND compensacao_horas.dt_falta BETWEEN '".trim($this->getDado("dt_falta_inicial"))."'\n";
        $stSql .= "                                       AND '".trim($this->getDado("dt_falta_final"))."'\n";
    }
    if (trim($this->getDado("dt_falta_inicial")) != ""  and trim($this->getDado("dt_falta_final")) == "") {
        $stSql .= "    AND compensacao_horas.dt_falta = '".trim($this->getDado("dt_falta_inicial"))."'\n";
    }
    if (trim($this->getDado("dt_falta_inicial")) == ""  and trim($this->getDado("dt_falta_final")) != "") {
        $stSql .= "    AND compensacao_horas.dt_falta = '".trim($this->getDado("dt_falta_final"))."'\n";
    }
    if (trim($this->getDado("dt_compensacao_inicial")) != "" and trim($this->getDado("dt_compensacao_final")) != "") {
        $stSql .= "    AND compensacao_horas.dt_compensacao BETWEEN '".trim($this->getDado("dt_compensacao_inicial"))."'\n";
        $stSql .= "                                            AND  '".trim($this->getDado("dt_compensacao_final"))."'\n";
    }
    if (trim($this->getDado("dt_compensacao_inicial")) != "" and trim($this->getDado("dt_compensacao_final")) == "") {
        $stSql .= "    AND compensacao_horas.dt_compensacao = '".trim($this->getDado("dt_compensacao_inicial"))."'\n";
    }
    if (trim($this->getDado("dt_compensacao_inicial")) == "" and trim($this->getDado("dt_compensacao_final")) != "") {
        $stSql .= "    AND compensacao_horas.dt_compensacao = '".trim($this->getDado("dt_compensacao_final"))."'\n";
    }
    $stSql .= "GROUP BY  cadastro.cod_local\n";
    $stSql .= "        , cadastro.desc_local\n";
    $stSql .= "        , compensacao_horas.dt_falta\n";
    $stSql .= "        , compensacao_horas.dt_compensacao\n";

    return $stSql;
}

function recuperaCompesacoesFuncao(&$rsRecordset,$stFiltro="",$stOrdem="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaCompesacoesFuncao",$rsRecordset,$stFiltro,$stOrdem,$boTransacao);
}

function montaRecuperaCompesacoesFuncao()
{
    $stSql .= "    SELECT cadastro.desc_regime_funcao||'/'||\n";
    $stSql .= "           cadastro.desc_sub_divisao_funcao||'/'||\n";
    $stSql .= "           cadastro.desc_funcao as descricao\n";
    $stSql .= "         , cadastro.cod_regime_funcao||'_'||\n";
    $stSql .= "           cadastro.cod_sub_divisao_funcao||'_'||\n";
    $stSql .= "           cadastro.cod_funcao||'_'||\n";
    $stSql .= "           coalesce(cadastro.cod_especialidade_funcao,0) as codigo\n";
    $stSql .= "         , to_char(compensacao_horas.dt_falta,'dd/mm/yyyy') as dt_falta\n";
    $stSql .= "         , to_char(compensacao_horas.dt_compensacao,'dd/mm/yyyy') as dt_compensacao\n";
    $stSql .= "      FROM ponto.compensacao_horas\n";
    $stSql .= "INNER JOIN (SELECT * \n";
    $stSql .= "              FROM recuperarContratoServidor('ef,rf,sf,f','".Sessao::getEntidade()."',0,'".$this->getDado('stTipoFiltro')."','".$this->getDado('stCodigos')."','".$this->getDado("exercicio")."')\n";
    $stSql .= "           ) as cadastro\n";
    $stSql .= "        ON compensacao_horas.cod_contrato = cadastro.cod_contrato\n";
    $stSql .= "     WHERE NOT EXISTS (SELECT 1\n";
    $stSql .= "                        FROM ponto.compensacao_horas_exclusao\n";
    $stSql .= "                       WHERE compensacao_horas_exclusao.cod_compensacao = compensacao_horas.cod_compensacao\n";
    $stSql .= "                         AND compensacao_horas_exclusao.cod_contrato = compensacao_horas.cod_contrato)\n";
    $stSql .= "       AND recuperarSituacaoDoContrato(cadastro.cod_contrato,0,'".Sessao::getEntidade()."') NOT IN ('R')\n";

    if (trim($this->getDado("dt_falta_inicial")) != ""  and trim($this->getDado("dt_falta_final")) != "") {
        $stSql .= "    AND compensacao_horas.dt_falta BETWEEN '".trim($this->getDado("dt_falta_inicial"))."'\n";
        $stSql .= "                                       AND '".trim($this->getDado("dt_falta_final"))."'\n";
    }
    if (trim($this->getDado("dt_falta_inicial")) != ""  and trim($this->getDado("dt_falta_final")) == "") {
        $stSql .= "    AND compensacao_horas.dt_falta = '".trim($this->getDado("dt_falta_inicial"))."'\n";
    }
    if (trim($this->getDado("dt_falta_inicial")) == ""  and trim($this->getDado("dt_falta_final")) != "") {
        $stSql .= "    AND compensacao_horas.dt_falta = '".trim($this->getDado("dt_falta_final"))."'\n";
    }
    if (trim($this->getDado("dt_compensacao_inicial")) != "" and trim($this->getDado("dt_compensacao_final")) != "") {
        $stSql .= "    AND compensacao_horas.dt_compensacao BETWEEN '".trim($this->getDado("dt_compensacao_inicial"))."'\n";
        $stSql .= "                                            AND  '".trim($this->getDado("dt_compensacao_final"))."'\n";
    }
    if (trim($this->getDado("dt_compensacao_inicial")) != "" and trim($this->getDado("dt_compensacao_final")) == "") {
        $stSql .= "    AND compensacao_horas.dt_compensacao = '".trim($this->getDado("dt_compensacao_inicial"))."'\n";
    }
    if (trim($this->getDado("dt_compensacao_inicial")) == "" and trim($this->getDado("dt_compensacao_final")) != "") {
        $stSql .= "    AND compensacao_horas.dt_compensacao = '".trim($this->getDado("dt_compensacao_final"))."'\n";
    }
    $stSql .= "GROUP BY  cadastro.cod_regime_funcao\n";
    $stSql .= "        , cadastro.cod_sub_divisao_funcao\n";
    $stSql .= "        , cadastro.cod_funcao\n";
    $stSql .= "        , cadastro.cod_especialidade_funcao\n";
    $stSql .= "        , cadastro.desc_regime_funcao\n";
    $stSql .= "        , cadastro.desc_sub_divisao_funcao\n";
    $stSql .= "        , cadastro.desc_funcao\n";
    $stSql .= "        , compensacao_horas.dt_falta\n";
    $stSql .= "        , compensacao_horas.dt_compensacao\n";

    return $stSql;
}

}
?>
