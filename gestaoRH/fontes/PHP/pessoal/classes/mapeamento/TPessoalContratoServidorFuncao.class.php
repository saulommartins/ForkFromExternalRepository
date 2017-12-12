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
  * Classe de mapeamento da tabela PESSOAL.CONTRATO_SERVIDOR_FUNCAO
  * Data de Criação: 14/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

  * @package URBEM
  * @subpackage Mapeamento

  Caso de uso: uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.CONTRATO_SERVIDOR_FUNCAO
  * Data de Criação: 14/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalContratoServidorFuncao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalContratoServidorFuncao()
{
    parent::Persistente();
    $this->setTabela('pessoal.contrato_servidor_funcao');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_contrato,cod_cargo,vigencia');

    $this->AddCampo('cod_contrato'  ,'integer'      ,true,  ''    ,true,    true );
    $this->AddCampo('cod_cargo'     ,'integer'      ,true,  ''    ,true,    true );
    $this->AddCampo('timestamp'     ,'timestamp'    ,false, ''    ,true,    false);
    $this->AddCampo('vigencia'      ,'date'         ,true,  ''    ,false,   false);

}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT contrato_servidor_funcao.cod_cargo as cod_funcao                                                      \n";
    $stSql .= "     , cargo.descricao                                                                                       \n";
    $stSql .= "     , to_char(contrato_servidor_nomeacao_posse.dt_posse,'dd/mm/yyyy') as dt_posse                           \n";
    $stSql .= "     , to_char(contrato_servidor_nomeacao_posse.dt_nomeacao,'dd/mm/yyyy') as dt_nomeacao                     \n";
    $stSql .= "     , to_char(contrato_servidor_nomeacao_posse.dt_admissao,'dd/mm/yyyy') as dt_admissao                     \n";
    $stSql .= "     , contrato.cod_contrato                                                                                 \n";
    $stSql .= "  FROM pessoal.contrato_servidor                                                                             \n";
    $stSql .= "     , pessoal.contrato                                                                                      \n";
    $stSql .= "     , pessoal.cargo                                                                                         \n";
    $stSql .= "     , pessoal.contrato_servidor_nomeacao_posse                                                              \n";
    $stSql .= "     , (  SELECT cod_contrato                                                                                \n";
    $stSql .= "               , max(timestamp) as timestamp                                                                 \n";
    $stSql .= "            FROM pessoal.contrato_servidor_nomeacao_posse                                                    \n";
    $stSql .= "        GROUP BY cod_contrato) as max_contrato_servidor_nomeacao_posse                                       \n";
    $stSql .= "     , pessoal.contrato_servidor_funcao                                                                      \n";
    $stSql .= "     , (  SELECT cod_contrato                                                                                \n";
    $stSql .= "               , max(timestamp) as timestamp                                                                 \n";
    $stSql .= "            FROM pessoal.contrato_servidor_funcao                                                            \n";
    $stSql .= "        GROUP BY cod_contrato) as max_contrato_servidor_funcao                                               \n";
    $stSql .= " WHERE contrato_servidor.cod_contrato = contrato_servidor_funcao.cod_contrato                                \n";
    $stSql .= "   AND contrato_servidor_funcao.cod_contrato = max_contrato_servidor_funcao.cod_contrato                     \n";
    $stSql .= "   AND contrato_servidor_funcao.timestamp = max_contrato_servidor_funcao.timestamp                           \n";
    $stSql .= "   AND contrato_servidor.cod_contrato = contrato.cod_contrato                                                \n";
    $stSql .= "   AND contrato_servidor_funcao.cod_cargo = cargo.cod_cargo                                                  \n";
    $stSql .= "   AND contrato_servidor.cod_contrato = contrato_servidor_nomeacao_posse.cod_contrato                        \n";
    $stSql .= "   AND contrato_servidor_nomeacao_posse.cod_contrato = max_contrato_servidor_nomeacao_posse.cod_contrato     \n";
    $stSql .= "   AND contrato_servidor_nomeacao_posse.timestamp    = max_contrato_servidor_nomeacao_posse.timestamp        \n";

    return $stSql;
}

function recuperaDeContratos(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaDeContratos",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaDeContratos()
{
    $stSql .= "SELECT contrato_servidor_funcao.*                                                                                                                        \n";
    $stSql .= "     , (SELECT descricao FROM pessoal.cargo WHERE cargo.cod_cargo = contrato_servidor_funcao.cod_cargo) as descricao       \n";
    $stSql .= "  FROM pessoal.contrato_servidor_funcao                                                                                        \n";
    $stSql .= "     , (  SELECT cod_contrato                                                                                                                                   \n";
    $stSql .= "               , max(timestamp) as timestamp                                                                                                                    \n";
    $stSql .= "            FROM pessoal.contrato_servidor_funcao                                                                              \n";
    $stSql .= "        GROUP BY cod_contrato) as max_contratro_servidor_funcao                                                                                          \n";
    $stSql .= " WHERE contrato_servidor_funcao.cod_contrato = max_contratro_servidor_funcao.cod_contrato                                                         \n";
    $stSql .= "   AND contrato_servidor_funcao.timestamp = max_contratro_servidor_funcao.timestamp                                                               \n";

    return $stSql;
}

function recuperaContratosFerias(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaContratosFerias",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaContratosFerias()
{
    $stSql .= "SELECT contrato_servidor_funcao.*                                                                         \n";
    $stSql .= "     , servidor.numcgm                                                                                   \n";
    $stSql .= "  FROM pessoal.contrato_servidor_funcao                                         \n";
    $stSql .= "     , ( SELECT cod_contrato                                                                             \n";
    $stSql .= "              , max(timestamp) as timestamp                                                              \n";
    $stSql .= "           FROM pessoal.contrato_servidor_funcao                                \n";
    $stSql .= "       GROUP BY cod_contrato) as max_contrato_servidor_funcao                                             \n";
    $stSql .= "     , pessoal.contrato_servidor_regime_funcao                                 \n";
    $stSql .= "     , (  SELECT cod_contrato                                                                            \n";
    $stSql .= "               , max(timestamp) as timestamp                                                             \n";
    $stSql .= "            FROM pessoal.contrato_servidor_regime_funcao                       \n";
    $stSql .= "        GROUP BY cod_contrato) as max_contrato_servidor_regime_funcao                                    \n";
    $stSql .= "     , pessoal.servidor_contrato_servidor                                      \n";
    $stSql .= "     , pessoal.servidor                                                        \n";
    $stSql .= " WHERE contrato_servidor_funcao.cod_contrato = max_contrato_servidor_funcao.cod_contrato                   \n";
    $stSql .= "   AND contrato_servidor_funcao.timestamp = max_contrato_servidor_funcao.timestamp                         \n";
    $stSql .= "   AND contrato_servidor_regime_funcao.cod_contrato = max_contrato_servidor_regime_funcao.cod_contrato   \n";
    $stSql .= "   AND contrato_servidor_regime_funcao.timestamp    = max_contrato_servidor_regime_funcao.timestamp      \n";
    $stSql .= "   AND contrato_servidor_regime_funcao.cod_contrato = contrato_servidor_funcao.cod_contrato               \n";
    $stSql .= "   AND contrato_servidor_funcao.cod_contrato = servidor_contrato_servidor.cod_contrato                    \n";
    $stSql .= "   AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                   \n";
    $stSql .= "   AND NOT EXISTS (SELECT 1                                                                              \n";
    $stSql .= "                     FROM pessoal.contrato_servidor_caso_causa                 \n";
    $stSql .= "                    WHERE contrato_servidor_caso_causa.cod_contrato = contrato_servidor_funcao.cod_contrato   )    \n";

    return $stSql;
}

function recuperaContratosDaFuncao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaContratosDaFuncao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaContratosDaFuncao()
{
    $stSql .= "SELECT contrato_servidor_funcao.*                                                            \n";
    $stSql .= "     , (SELECT registro FROM pessoal.contrato where cod_contrato = contrato_servidor_funcao.cod_contrato) as registro  \n";
    $stSql .= "     , (SELECT nom_cgm FROM sw_cgm WHERE numcgm = servidor.numcgm) as nom_cgm                \n";
    $stSql .= "  FROM pessoal.contrato_servidor_funcao                             \n";
    $stSql .= "  JOIN pessoal.servidor_contrato_servidor                           \n";
    $stSql .= "    ON contrato_servidor_funcao.cod_contrato = servidor_contrato_servidor.cod_contrato       \n";
    $stSql .= "  JOIN pessoal.servidor                                             \n";
    $stSql .= "    ON servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                       \n";

    $stSql .= "  JOIN (  SELECT cod_contrato                                                                \n";
    $stSql .= "               , MAX(timestamp) as timestamp                                                 \n";
    $stSql .= "            FROM pessoal.contrato_servidor_funcao                   \n";
    $stSql .= "        GROUP BY cod_contrato) as max_contrato_servidor_funcao                               \n";
    $stSql .= "    ON contrato_servidor_funcao.cod_contrato = max_contrato_servidor_funcao.cod_contrato     \n";
    $stSql .= "   AND contrato_servidor_funcao.timestamp = max_contrato_servidor_funcao.timestamp           \n";

    $stSql .= "  JOIN pessoal.contrato_servidor_sub_divisao_funcao                 \n";
    $stSql .= "    ON contrato_servidor_funcao.cod_contrato = contrato_servidor_sub_divisao_funcao.cod_contrato\n";
    $stSql .= "  JOIN (  SELECT cod_contrato                                                                \n";
    $stSql .= "               , MAX(timestamp) as timestamp                                                 \n";
    $stSql .= "            FROM pessoal.contrato_servidor_sub_divisao_funcao       \n";
    $stSql .= "        GROUP BY cod_contrato) as max_contrato_servidor_sub_divisao_funcao                   \n";
    $stSql .= "    ON contrato_servidor_sub_divisao_funcao.cod_contrato = max_contrato_servidor_sub_divisao_funcao.cod_contrato     \n";
    $stSql .= "   AND contrato_servidor_sub_divisao_funcao.timestamp = max_contrato_servidor_sub_divisao_funcao.timestamp           \n";

    $stSql .= "  JOIN pessoal.contrato_servidor_regime_funcao                      \n";
    $stSql .= "    ON contrato_servidor_funcao.cod_contrato = contrato_servidor_regime_funcao.cod_contrato  \n";
    $stSql .= "  JOIN (  SELECT cod_contrato                                                                \n";
    $stSql .= "               , MAX(timestamp) as timestamp                                                 \n";
    $stSql .= "            FROM pessoal.contrato_servidor_regime_funcao            \n";
    $stSql .= "        GROUP BY cod_contrato) as max_contrato_servidor_regime_funcao                        \n";
    $stSql .= "    ON contrato_servidor_regime_funcao.cod_contrato = max_contrato_servidor_regime_funcao.cod_contrato     \n";
    $stSql .= "   AND contrato_servidor_regime_funcao.timestamp = max_contrato_servidor_regime_funcao.timestamp           \n";

    $stSql .= "LEFT JOIN pessoal.contrato_servidor_especialidade_funcao            \n";
    $stSql .= "       ON contrato_servidor_funcao.cod_contrato = contrato_servidor_especialidade_funcao.cod_contrato \n";
    $stSql .= "LEFT JOIN (  SELECT cod_contrato                                                             \n";
    $stSql .= "               , MAX(timestamp) as timestamp                                                 \n";
    $stSql .= "            FROM pessoal.contrato_servidor_especialidade_funcao     \n";
    $stSql .= "        GROUP BY cod_contrato) as max_contrato_servidor_especialidade_funcao                 \n";
    $stSql .= "    ON contrato_servidor_especialidade_funcao.cod_contrato = max_contrato_servidor_especialidade_funcao.cod_contrato     \n";
    $stSql .= "   AND contrato_servidor_especialidade_funcao.timestamp = max_contrato_servidor_especialidade_funcao.timestamp           \n";

    return $stSql;
}

}
