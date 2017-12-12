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
* Classe de mapeamento da tabela responsavel_tecnico
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 8905 $
$Name$
$Author: cercato $
$Date: 2006-04-26 11:45:32 -0300 (Qua, 26 Abr 2006) $

Casos de uso: uc-01.02.98,
              uc-05.02.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TResponsavelTecnico extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TResponsavelTecnico()
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

function recuperaResponsavelContabil(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaResponsavelContabil().$stCondicao.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaResponsavelContabil()
{
/*
    //antigo alterado para correcao do bug#5463
    //$stQuebra = "<br>";
    $stSql .= " SELECT                                                ".$stQuebra;
    $stSql .= "     rp.sequencia,                                     ".$stQuebra;
    $stSql .= "     rp.numcgm,                                        ".$stQuebra;
    $stSql .= "     rp.cod_profissao,                                 ".$stQuebra;
    $stSql .= "     rp.num_registro,                                  ".$stQuebra;
    $stSql .= "     rp.cod_uf,                                        ".$stQuebra;
    $stSql .= "     cgm.nom_cgm,                                      ".$stQuebra;
    $stSql .= "     p.nom_profissao,                                  ".$stQuebra;
    $stSql .= "     c.nom_registro,                                   ".$stQuebra;
    $stSql .= "     uf.nom_uf                                         ".$stQuebra;
    $stSql .= " FROM                                                  ".$stQuebra;
    $stSql .= "     ".$this->getTabela()." AS rp,                     ".$stQuebra;
    $stSql .= "     cse.profissao          AS p,                  ".$stQuebra;
    $stSql .= "     cse.conselho           AS c,                  ".$stQuebra;
    $stSql .= "     sw_uf                 AS uf,                     ".$stQuebra;
    $stSql .= "     sw_cgm as cgm                                    ".$stQuebra;
    $stSql .= " WHERE                                                 ".$stQuebra;
    $stSql .= "     rp.cod_profissao IN (                             ".$stQuebra;
    $stSql .= "         SELECT                                        ".$stQuebra;
    $stSql .= "             valor                                     ".$stQuebra;
    $stSql .= "         FROM                                          ".$stQuebra;
    $stSql .= "             administracao.configuracao                          ".$stQuebra;
    $stSql .= "         WHERE                                         ".$stQuebra;
    $stSql .= "             parametro = 'cod_contador' or             ".$stQuebra;
    $stSql .= "             parametro = 'cod_tec_contabil' ) AND      ".$stQuebra;
    $stSql .= "     cgm.numcgm        = rp.numcgm            AND      ".$stQuebra;
    $stSql .= "     rp.cod_profissao  = p.cod_profissao      AND      ".$stQuebra;
    $stSql .= "     p.cod_conselho    = c.cod_conselho       AND      ".$stQuebra;
    $stSql .= "     rp.cod_uf         = uf.cod_uf                     ".$stQuebra;
*/

    $stSql = "    SELECT                                                                                \n";
    $stSql .= "        CASE                                                                             \n";
    $stSql .= "            WHEN rp.numcgm IS NOT NULL THEN rp.numcgm                                    \n";
    $stSql .= "            WHEN re.numcgm IS NOT NULL THEN re.numcgm                                    \n";
    $stSql .= "        END AS numcgm,                                                                   \n";
    $stSql .= "        CASE                                                                             \n";
    $stSql .= "            WHEN rp.sequencia IS NOT NULL THEN rp.sequencia                              \n";
    $stSql .= "            WHEN re.sequencia IS NOT NULL THEN re.sequencia                              \n";
    $stSql .= "        END AS sequencia,                                                                \n";
    $stSql .= "        rp.cod_profissao,                                                                \n";
    $stSql .= "        rp.num_registro,                                                                 \n";
    $stSql .= "        rp.cod_uf,                                                                       \n";
    $stSql .= "        cgm.nom_cgm,                                                                     \n";
    $stSql .= "        rp.nom_profissao,                                                                \n";
    $stSql .= "        rp.nom_registro,                                                                 \n";
    $stSql .= "        rp.nom_uf                                                                        \n";
    $stSql .= "    from                                                                                 \n";
    $stSql .= "        sw_cgm as cgm                                                                    \n";
    $stSql .= "    left join                                                                            \n";
    $stSql .= "        (                                                                                \n";
    $stSql .= "        SELECT                                                                           \n";
    $stSql .= "            rp.*,                                                                        \n";
    $stSql .= "            p.nom_profissao,                                                             \n";
    $stSql .= "            c.nom_registro,                                                              \n";
    $stSql .= "            uf.nom_uf                                                                    \n";
    $stSql .= "        FROM                                                                             \n";
    $stSql .= "            economico.responsavel_tecnico AS rp,                                         \n";
    $stSql .= "            cse.profissao          AS p,                                                 \n";
    $stSql .= "            cse.conselho           AS c,                                                 \n";
    $stSql .= "            sw_uf                 AS uf                                                  \n";
    $stSql .= "        WHERE                                                                            \n";
    $stSql .= "            rp.cod_profissao::varchar IN (                                                        \n";
    $stSql .= "                SELECT                                                                   \n";
    $stSql .= "                    valor                                                                \n";
    $stSql .= "                FROM                                                                     \n";
    $stSql .= "                    administracao.configuracao                                           \n";
    $stSql .= "                WHERE                                                                    \n";
    $stSql .= "                    parametro = 'cod_contador' or                                        \n";
    $stSql .= "                    parametro = 'cod_tec_contabil' ) AND                                 \n";
    $stSql .= "            rp.cod_profissao  = p.cod_profissao      AND                                 \n";
    $stSql .= "            p.cod_conselho    = c.cod_conselho       AND                                 \n";
    $stSql .= "            rp.cod_uf         = uf.cod_uf                                                \n";
    $stSql .= "        ) AS rp                                                                \n";
    $stSql .= "    on                                                                                   \n";
    $stSql .= "        rp.numcgm=cgm.numcgm                                                             \n";
    $stSql .= "    left join                                                                            \n";
    $stSql .= "        (                                                                                \n";
    $stSql .= "            SELECT DISTINCT                                                              \n";
    $stSql .= "                    numcgm,sequencia                                                     \n";
    $stSql .= "            FROM                                                                         \n";
    $stSql .= "                    economico.responsavel_empresa                                        \n";
    $stSql .= "        ) as re                                                                          \n";
    $stSql .= "    on                                                                                   \n";
    $stSql .= "        re.numcgm=cgm.numcgm                                                             \n";
    $stSql .= "    WHERE                                                                                \n";
    $stSql .= "        (rp.numcgm = cgm.numcgm or re.numcgm = cgm.numcgm)                               \n";

    return $stSql;
}

function montaRecuperaRelacionamento()
{
    //$stQuebra = "<br>";
    $stSql .= " select                              ".$stQuebra;
    $stSql .= "     c.cod_conselho,                 ".$stQuebra;
    $stSql .= "     c.nom_conselho,                 ".$stQuebra;
    $stSql .= "     c.nom_registro,                 ".$stQuebra;
    $stSql .= "     p.cod_profissao,                ".$stQuebra;
    $stSql .= "     p.nom_profissao                 ".$stQuebra;
    $stSql .= " from                                ".$stQuebra;
    $stSql .= "     ".CONSELHO." as c,              ".$stQuebra;
    $stSql .= "     ".PROFISSAO." as p              ".$stQuebra;
    $stSql .= " where                               ".$stQuebra;
    $stSql .= "     c.cod_conselho = p.cod_conselho ".$stQuebra;
    //echo $stSql;
    return $stSql;
}

}
