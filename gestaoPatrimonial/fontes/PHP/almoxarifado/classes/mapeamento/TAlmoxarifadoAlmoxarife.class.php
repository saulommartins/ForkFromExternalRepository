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
    * Classe de mapeamento da tabela ALMOXARIFADO.ALMOXARIFE
    * Data de Criação: 26/10/2005

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 12234 $
    $Name$
    $Author: diego $
    $Date: 2006-07-06 11:08:37 -0300 (Qui, 06 Jul 2006) $

    * Casos de uso: uc-03.03.02
                    uc-03.03.14
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

/**
  * Efetua conexão com a tabela  ALMOXARIFADO.ALMOXARIFE
  * Data de Criação: 26/10/2005

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Fernando Zank Correa Evangelista

  * @package URBEM
  * @subpackage Mapeamento
*/
class TAlmoxarifadoAlmoxarife extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TAlmoxarifadoAlmoxarife()
{
    parent::Persistente();
    $this->setTabela('almoxarifado.almoxarife');

    $this->setCampoCod('cgm_almoxarife');
    $this->setComplementoChave('');

    $this->AddCampo('cgm_almoxarife','integer',true,'',true,'TAdministracaoUsuario','numcgm');
    $this->AddCampo('ativo','boolean',true,'',false,false);

}

function recuperaRelacionamentoTodos(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaRelacionamentoTodos().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaRelacionamentoTodos()
{

    $stSql.="     SELECT almoxarife.cgm_almoxarife                                                                                                  \n";
    $stSql.="            ,TRIM(sw_cgm2.nom_cgm) AS nom_cgm                                                                                                         \n";
    $stSql.="            ,usuario.username,                                                                                                          \n";
    $stSql.="  CASE WHEN almoxarife.ativo IS true                                                                                                   \n";                 $stSql.="       THEN 'Ativo'                                                                                                                    \n";
    $stSql.="       ELSE 'Inativo'                                                                                                                  \n";
    $stSql.="        END AS status                                                                                                                  \n";
    $stSql.="           ,publico.concatenar_nova_linha(almoxarifado.cod_almoxarifado || ' - ' || sw_cgm1.nom_cgm) AS almoxarifados                  \n";
    $stSql.="       FROM almoxarifado.almoxarife                                                                                                    \n";
    $stSql.="  LEFT JOIN almoxarifado.permissao_almoxarifados On(permissao_almoxarifados.cgm_almoxarife = almoxarife.cgm_almoxarife               ) \n";
    $stSql.="  LEFT JOIN almoxarifado.almoxarifado            On(almoxarifado.cod_almoxarifado          = permissao_almoxarifados.cod_almoxarifado) \n";
    $stSql.="  LEFT JOIN sw_cgm AS sw_cgm1                    ON(sw_cgm1.numcgm                        = almoxarifado.cgm_almoxarifado            ) \n";
    $stSql.="       ,sw_cgm AS sw_cgm2                                                                                                              \n";
    $stSql.="       ,administracao.usuario                                                                                                              \n";
    $stSql.="      WHERE almoxarife.cgm_almoxarife = sw_cgm2.numcgm                                                                                 \n";
    $stSql.="        AND usuario.numcgm = almoxarife.cgm_almoxarife                                                                       \n";

    return $stSql;
}

function montaRecuperaRelacionamento()
{
    $stSql =  "select                                               \n";
    $stSql .= "       a1.cgm_almoxarife,                            \n";
    $stSql .= "       a1_cgm.nom_cgm,                               \n";
    $stSql .= "       case when a1.ativo is true then 'Ativo'       \n";
    $stSql .= "       else 'Inativo' end as status,                 \n";
    $stSql .= "       publico.concatenar_nova_linha(a2.cod_almoxarifado || ' - ' || a2_cgm.nom_cgm) as almoxarifados, \n";
    $stSql .= "       p.padrao                                      \n";
    $stSql .= "from                                                 \n";
    $stSql .= "       almoxarifado.almoxarife a1,                   \n";
    $stSql .= "       almoxarifado.almoxarifado a2,                 \n";
    $stSql .= "       almoxarifado.permissao_almoxarifados p,       \n";
    $stSql .= "       sw_cgm as a1_cgm,                             \n";
    $stSql .= "       sw_cgm as a2_cgm                              \n";
    $stSql .= "where                                                \n";
    $stSql .= "        a1.cgm_almoxarife = a1_cgm.numcgm  and       \n";
    $stSql .= "        a1.cgm_almoxarife = p.cgm_almoxarife and     \n";
    $stSql .= "        a2.cod_almoxarifado = p.cod_almoxarifado and \n";
    $stSql .= "        a2.cgm_almoxarifado = a2_cgm.numcgm          \n";

    return $stSql;
}

function recuperaAlmoxarifePermissoes(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaAlmoxarifePermissoes().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaAlmoxarifePermissoes()
{
    $stSql =  "SELECT (a2.cod_almoxarifado || ' - ' ||a2_cgm.nom_cgm) as almoxarifados \n";
    $stSql .= "  FROM almoxarifado.almoxarife a1                                       \n";
    $stSql .= "     , almoxarifado.almoxarifado a2                                     \n";
    $stSql .= "     , almoxarifado.permissao_almoxarifados p                           \n";
    $stSql .= "     , sw_cgm as a1_cgm                                                 \n";
    $stSql .= "     , sw_cgm as a2_cgm                                                 \n";
    $stSql .= " WHERE a1.cgm_almoxarife = a1_cgm.numcgm                                \n";
    $stSql .= "   AND a1.cgm_almoxarife = p.cgm_almoxarife                             \n";
    $stSql .= "   AND a2.cod_almoxarifado = p.cod_almoxarifado                         \n";
    $stSql .= "   AND a2.cgm_almoxarifado = a2_cgm.numcgm                              \n";

    return $stSql;
}

}
