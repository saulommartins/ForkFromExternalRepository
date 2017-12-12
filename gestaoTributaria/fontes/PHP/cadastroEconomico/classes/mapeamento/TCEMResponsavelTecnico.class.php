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
  * Classe de mapeamento da tabela ECONOMICO.RESPONSAVEL_TECNICO
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TCEMResponsavelTecnico.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.02.04
*/

/*
$Log$
Revision 1.14  2007/03/27 19:29:00  rodrigo
Bug #8768#

Revision 1.13  2007/03/19 15:52:37  cercato
Bug #8774#

Revision 1.12  2007/02/16 10:09:24  rodrigo
#6953#

Revision 1.11  2006/11/23 15:54:39  cassiano
#ga_1.35.4#

Revision 1.10  2006/09/15 12:08:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ECONOMICO.RESPONSAVEL_TECNICO
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCEMResponsavelTecnico extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCEMResponsavelTecnico()
{
    parent::Persistente();
    $this->setTabela('economico.responsavel_tecnico');

    $this->setCampoCod('');
    $this->setComplementoChave('numcgm,cod_profissao,sequencia,cod_uf');

    $this->AddCampo('numcgm','integer',true,'',true,true);
    $this->AddCampo('sequencia','integer',true,'',true,true);
    $this->AddCampo('cod_profissao','integer',true,'',false,true);
    $this->AddCampo('num_registro','varchar',true,'',false,false);
    $this->AddCampo('cod_uf','integer',true,'',false,true);

}

function recuperaTecnico(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaTecnico().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaTecnico()
{
    $stSql = "SELECT sw_cgm.nom_cgm                                                                  \n";
    $stSql.= "      ,sw_cgm.numcgm                                                                   \n";
    $stSql.= "      ,profissao.cod_profissao                                                         \n";
    $stSql.= "      ,profissao.nom_profissao                                                         \n";
    $stSql.= "      ,responsavel.sequencia                                                           \n";
    $stSql.= "      ,responsavel_tecnico.num_registro                                                \n";
    $stSql.= "      ,sw_uf.sigla_uf                                                                  \n";
    $stSql.= "  FROM cse.profissao                                                                   \n";
    $stSql.= "      ,cse.conselho                                                                    \n";
    $stSql.= "      ,sw_cgm                                                                          \n";
    $stSql.= "      ,sw_uf                                                                           \n";
    $stSql.= "      ,economico.responsavel_tecnico                                                   \n";
    $stSql.= "  LEFT JOIN (economico.responsavel_empresa JOIN economico.empresa_profissao            \n";
    $stSql.= "                       ON(responsavel_empresa.numcgm = empresa_profissao.numcgm))      \n";
    $stSql.= "   ON responsavel_tecnico.numcgm    = responsavel_empresa.numcgm_resp_tecnico          \n";
    $stSql.= "        AND responsavel_tecnico.sequencia = responsavel_empresa.sequencia_resp_tecnico \n";
    $stSql.= "      ,economico.responsavel                                                           \n";
    $stSql.= " WHERE responsavel.numcgm                = sw_cgm.numcgm                               \n";
    $stSql.= "   AND sw_cgm.cod_uf                     = sw_uf.cod_uf                                \n";
    $stSql.= "   AND((responsavel_tecnico.numcgm       = responsavel.numcgm                          \n";
    $stSql.= "   AND responsavel_tecnico.sequencia     = responsavel.sequencia)                      \n";
    $stSql.= "    OR(responsavel_empresa.numcgm        = responsavel.numcgm                          \n";
    $stSql.= "   AND responsavel_empresa.sequencia     = responsavel.sequencia))                     \n";
    $stSql.= "   AND responsavel_tecnico.cod_profissao = profissao.cod_profissao                     \n";
    $stSql.= "   AND profissao.cod_conselho          = conselho.cod_conselho                         \n";

    return $stSql;
}

function recuperaRelacionamento(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaRelacionamento().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamento()
{
    $stSql  .= "SELECT                                              \r\n";
    $stSql  .= "    numcgm,                                         \r\n";
    $stSql  .= "    nom_cgm,                                        \r\n";
    $stSql  .= "    cod_profissao,                                  \r\n";
    $stSql  .= "    nom_profissao,                                  \r\n";
    $stSql  .= "    nom_registro,                                   \r\n";
    $stSql  .= "    num_registro,                                   \r\n";
    $stSql  .= "    cod_uf,                                         \r\n";
    $stSql  .= "    nom_uf,                                         \r\n";
    $stSql  .= "    sigla_uf,                                       \r\n";
    $stSql  .= "    sequencia                                       \r\n";
    $stSql  .= "FROM                                                \r\n";
    $stSql  .= "    economico.vw_responsavel_tecnico                \r\n";

    return $stSql;
}

function recuperaRelacionamentoRelatorio(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if ($stOrder) {
        $stOrder = " ORDER BY ".$stOrder;
    }
    $stSql = $this->montaRecuperaRelacionamentoRelatorio().$stFiltro.$stOrder;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoRelatorio()
{
$stSql .= " SELECT                                                                      \n";
$stSql .= "     RT.num_registro,                                                        \n";
$stSql .= "     CGM.numcgm||' '||CGM.nom_cgm AS contador,                               \n";
$stSql .= "     CGM.nom_cgm,                                                            \n";
$stSql .= "     CGM.logradouro||', '||CGM.numero||' '||CGM.complemento AS endereco,     \n";
$stSql .= "     CGM.fone_comercial,                                                     \n";
$stSql .= "     CE.inscricao_economica,                                                 \n";
$stSql .= "     L.endereco_cadastro                                                     \n";
$stSql .= " FROM                                                                        \n";
$stSql .= "     economico.responsavel_tecnico RT                                        \n";
$stSql .= "     INNER JOIN economico.cadastro_econ_resp_contabil CER ON                 \n";
$stSql .= "         CER.numcgm          = RT.numcgm                                     \n";
//$stSql .= "   AND CER.cod_profissao   = RT.cod_profissao                              \n";
$stSql .= "     LEFT JOIN economico.cadastro_economico CE ON                            \n";
$stSql .= "         CE.inscricao_economica = CER.inscricao_economica                    \n";
$stSql .= "     LEFT JOIN economico.domicilio_fiscal DF ON                              \n";
$stSql .= "         DF.inscricao_economica = CE.inscricao_economica                     \n";
$stSql .= "     LEFT JOIN (                                                             \n";
$stSql .= "         SELECT                                                              \n";
$stSql .= "             I.inscricao_municipal,                                          \n";
$stSql .= "             TL.nom_tipo||' '||NL.nom_logradouro AS endereco_cadastro        \n";
$stSql .= "         FROM                                                                \n";
$stSql .= "             imobiliario.imovel               I,                             \n";
$stSql .= "             imobiliario.imovel_confrontacao IC,                             \n";
$stSql .= "             imobiliario.confrontacao_trecho CT,                             \n";
$stSql .= "             sw_logradouro                    L,                             \n";
$stSql .= "             sw_nome_logradouro              NL,                             \n";
$stSql .= "             sw_tipo_logradouro              TL                              \n";
$stSql .= "         WHERE                                                               \n";
$stSql .= "             I.inscricao_municipal = IC.inscricao_municipal AND              \n";
$stSql .= "             IC.cod_lote           = CT.cod_lote            AND              \n";
$stSql .= "             CT.cod_logradouro     = L.cod_logradouro       AND              \n";
$stSql .= "             L.cod_logradouro      = NL.cod_logradouro      AND              \n";
$stSql .= "             NL.cod_tipo           = TL.cod_tipo                             \n";
$stSql .= "     ) AS L ON                                                               \n";
$stSql .= "         L.inscricao_municipal = DF.inscricao_municipal,                     \n";
$stSql .= "     sw_cgm CGM                                                              \n";
$stSql .= " WHERE                                                                       \n";
$stSql .= "     RT.numcgm = CGM.numcgm AND                                              \n";
$stSql .= "     RT.cod_profissao IN (
                                      SELECT valor::integer
                                      FROM administracao.configuracao
                                      WHERE (   parametro = 'cod_contador' OR
                                                parametro = 'cod_tec_contabil'))        \n";
return $stSql;

}

function recuperaProfissoes(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaProfissoes().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaProfissoes()
{
    $stSql .="    SELECT                                       \n";
    $stSql .="        *                                        \n";
    $stSql .="    FROM                                         \n";
    $stSql .="        cse.profissao                            \n";

    return $stSql;
}

}
