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
  * Classe de mapeamento da tabela ECONOMICO.ELEMENTO_ATIVIDADE
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TCEMElementoAtividade.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.02.07
*/

/*
$Log$
Revision 1.14  2007/03/20 14:40:33  cassiano
Bug #8771#

Revision 1.13  2006/11/20 15:23:57  cercato
bug #7438#

Revision 1.11  2006/11/17 11:08:26  dibueno
Bug #7519#

Revision 1.10  2006/09/15 12:08:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ECONOMICO.ELEMENTO_ATIVIDADE
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCEMElementoAtividade extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCEMElementoAtividade()
{
    parent::Persistente();
    $this->setTabela('economico.elemento_atividade');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_atividade,cod_elemento');

    $this->AddCampo('cod_atividade','integer',true,'',true,true);
    $this->AddCampo('cod_elemento','integer',true,'',true,true);
    $this->AddCampo('ativo','boolean',true,'',false,false);

}

function recuperaElementoAtividade(&$rsRecordSet, $stFiltro ='', $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaElementoAtividade().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaElementoAtividadeSelecionados(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaElementoAtividadeSelecionados().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaElementoAtividadeDisponiveis(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaElementoAtividadeDisponiveis().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaElementoInscricao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaElementoInscricao($stFiltro).$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaElementoAtividadeEconomico(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaElementoAtividadeEconomico().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaElementoAtividadeEconomico()
{
    $stSql .="  SELECT                                           \n";
    $stSql .="      EA.*,                                        \n";
    $stSql .="      E.NOM_ELEMENTO                               \n";
    $stSql .="  FROM                                             \n";
    $stSql .="    ( SELECT                                       \n";
    $stSql .="       inscricao_economica,cod_atividade , ocorrencia_atividade,  cod_elemento , max(ocorrencia_elemento) as ocorrencia_elemento \n";
    $stSql .="      FROM economico.elemento_ativ_cad_economico   \n";
    $stSql .="      GROUP BY inscricao_economica , cod_atividade , ocorrencia_atividade , cod_elemento \n";
    $stSql .="      ORDER BY ocorrencia_elemento desc) AS EA,    \n";
    $stSql .="      economico.elemento AS E                        \n";
    $stSql .="  WHERE                                            \n";
    $stSql .="      EA.COD_ELEMENTO = E.COD_ELEMENTO             \n";

    return $stSql;
}

function montaRecuperaElementoInscricao($filtro)
{
    $stSql .="    SELECT                                         \n";
    $stSql .="        EL.cod_elemento,                           \n";
    $stSql .="        EL.nom_elemento,                           \n";
    $stSql .="        AC.cod_atividade,                          \n";
    $stSql .="        max(AC.ocorrencia_atividade) as ocorrencia_atividade, \n";
    $stSql .="        A.cod_estrutural                           \n";
    $stSql .="    FROM                                           \n";
    $stSql .="        economico.elemento as EL,                    \n";
    $stSql .="        economico.atividade_cadastro_economico as AC,\n";
    $stSql .="        economico.elemento_atividade as AE,          \n";
    $stSql .="        economico.atividade A                        \n";
    $stSql .="    WHERE                                          \n";
    $stSql .="        AC.cod_atividade = AE.cod_atividade AND    \n";
    $stSql .="        AE.cod_elemento = EL.cod_elemento   AND    \n";
    $stSql .="        AE.cod_atividade= A.cod_atividade         \n";
    $stSql .= $filtro." group by
                            EL.cod_elemento,
                            EL.nom_elemento,
                            AC.cod_atividade,
                            A.cod_estrutural ";

    return $stSql;
}

function montaRecuperaElementoAtividade()
{
    $stSql .="    SELECT                                      \n";

    $stSql .="        A.cod_atividade,                        \n";

    $stSql .="        EL.cod_elemento,                        \n";
    $stSql .="        EL.nom_elemento                         \n";
    $stSql .="    FROM                                        \n";
    $stSql .="        economico.elemento as EL,                 \n";
    $stSql .="        economico.atividade as A,                 \n";
    $stSql .="        economico.elemento_atividade as AE        \n";
    $stSql .="    WHERE                                       \n";
    $stSql .="        A.cod_atividade = AE.cod_atividade AND  \n";
    $stSql .="        AE.cod_elemento = EL.cod_elemento       \n";

    return $stSql;
}

function montaRecuperaElementoAtividadeSelecionados()
{
    $stSql .="    SELECT                                   \n ";
    $stSql .="        EL.COD_ELEMENTO,                     \n ";
    $stSql .="        EL.NOM_ELEMENTO                      \n ";
    $stSql .="    FROM                                     \n ";
    $stSql .="        economico.elemento AS EL               \n ";
    $stSql .="    LEFT JOIN                                \n ";
    $stSql .="        economico.elemento_atividade AS AE     \n ";
    $stSql .="    ON                                       \n ";
    $stSql .="        EL.COD_ELEMENTO = AE.COD_ELEMENTO    \n ";
    $stSql .="    WHERE                                    \n ";
    $stSql .="        AE.COD_ELEMENTO IS NOT NULL          \n ";

    return $stSql;
}

function montaRecuperaElementoAtividadeDisponiveis()
{
    $stSql .="    SELECT                                   \n ";
    $stSql .="        EL.COD_ELEMENTO,                     \n ";
    $stSql .="        EL.NOM_ELEMENTO                      \n ";
    $stSql .="    FROM                                     \n ";
    $stSql .="        economico.elemento AS EL               \n ";
   /* $stSql .="    LEFT JOIN                                \n ";
    $stSql .="        economico.elemento_atividade AS AE     \n ";
    $stSql .="    ON                                       \n ";
    $stSql .="        EL.COD_ELEMENTO = AE.COD_ELEMENTO    \n ";
    $stSql .="    WHERE                                    \n ";
    $stSql .="        AE.COD_ELEMENTO IS NULL              \n ";*/

    return $stSql;
}

function recuperaElementoInscricaoAtividade(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaElementoInscricaoAtividade().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaElementoInscricaoAtividade()
{
    $stSql .="  SELECT                                                                  \n";
    $stSql .="      EL.cod_elemento,                                                    \n";
    $stSql .="      EL.nom_elemento,                                                    \n";
    $stSql .="      AC.cod_atividade,                                                   \n";
    $stSql .="      AC.ocorrencia_atividade                                             \n";
    $stSql .="  FROM                                                                    \n";
    $stSql .="      economico.elemento as EL                                            \n";
    $stSql .="      LEFT JOIN economico.elemento_atividade as AE                        \n";
    $stSql .="      ON                                                                  \n";
    $stSql .="          EL.cod_elemento = AE.cod_elemento                               \n";
    $stSql .="      LEFT JOIN economico.baixa_elemento as BE                            \n";
    $stSql .="      ON                                                                  \n";
    $stSql .="          EL.cod_elemento = BE.cod_elemento                               \n";
    $stSql .="      INNER JOIN economico.atividade_cadastro_economico AC                \n";
    $stSql .="      ON                                                                  \n";
    $stSql .="          AE.cod_atividade = AC.cod_atividade                             \n";
    $stSql .="      INNER JOIN                                                          \n";
    $stSql .="          economico.elemento_ativ_cad_economico AS EAI                    \n";
    $stSql .="      ON                                                                  \n";
    $stSql .="          EAI.cod_atividade  = AE.cod_atividade                           \n";
    $stSql .="          AND EAI.cod_elemento  = AE.cod_elemento                         \n";
    $stSql .="          AND EAI.inscricao_economica = AC.inscricao_economica            \n";
    $stSql .="          AND EAI.ocorrencia_atividade = AC.ocorrencia_atividade          \n";
    $stSql .="  WHERE                                                                   \n";
    $stSql .="      BE.cod_elemento is null
                    AND AC.ocorrencia_atividade = (
                            SELECT
                                MAX( atividade_cadastro_economico.ocorrencia_atividade )
                            FROM
                                economico.atividade_cadastro_economico
                            WHERE
                                atividade_cadastro_economico.inscricao_economica = AC.inscricao_economica
                    ) \n";

    return $stSql;
}

function recuperaElementoPorUltimaOcorrenciaAtividadeEconomica(&$rsRecordSet, $inInscricaoEconomica, $stFiltro = '', $stOrder = '', $boTransacao = '')
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaElementoPorUltimaOcorrenciaAtividadeEconomica($inInscricaoEconomica).$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaElementoPorUltimaOcorrenciaAtividadeEconomica($inInscricaoEconomica)
{
    $stSQL  = " SELECT                                                                                              \n";
    $stSQL .= "     atividade_cadastro_economico.ocorrencia_atividade                                               \n";
    $stSQL .= "     ,atividade_cadastro_economico.inscricao_economica                                               \n";
    $stSQL .= "     ,atividade.cod_atividade                                                                        \n";
    $stSQL .= "     ,atividade.nom_atividade                                                                        \n";
    $stSQL .= "     ,elemento.cod_elemento                                                                          \n";
    $stSQL .= "     ,elemento.nom_elemento                                                                          \n";
    $stSQL .= " FROM                                                                                                \n";
    $stSQL .= "     (SELECT                                                                                         \n";
    $stSQL .= "         max(ocorrencia_atividade) as ocorrencia_atividade                                           \n";
    $stSQL .= "         ,inscricao_economica                                                                        \n";
    $stSQL .= "      FROM                                                                                           \n";
    $stSQL .= "         economico.atividade_cadastro_economico                                                      \n";
    $stSQL .= "      WHERE                                                                                          \n";
    $stSQL .= "         inscricao_economica = $inInscricaoEconomica                                                 \n";
    $stSQL .= "      GROUP BY                                                                                       \n";
    $stSQL .= "         inscricao_economica) AS atividade_cadastro_economico_ocorrencia                             \n";
    $stSQL .= "     ,economico.atividade_cadastro_economico                                                         \n";
    $stSQL .= "     ,economico.atividade                                                                            \n";
    $stSQL .= "     ,economico.elemento_atividade                                                                   \n";
    $stSQL .= "     ,economico.elemento                                                                             \n";
    $stSQL .= " WHERE                                                                                               \n";
    $stSQL .= "     atividade_cadastro_economico_ocorrencia.inscricao_economica      = atividade_cadastro_economico.inscricao_economica  \n";
    $stSQL .= "     AND atividade_cadastro_economico_ocorrencia.ocorrencia_atividade = atividade_cadastro_economico.ocorrencia_atividade \n";
    $stSQL .= "     AND atividade_cadastro_economico.cod_atividade                   = atividade.cod_atividade                           \n";
    $stSQL .= "     AND atividade.cod_atividade                                      = elemento_atividade.cod_atividade                  \n";
    $stSQL .= "     AND elemento_atividade.cod_elemento                              = elemento.cod_elemento                             \n";

    return $stSQL;
}

}
