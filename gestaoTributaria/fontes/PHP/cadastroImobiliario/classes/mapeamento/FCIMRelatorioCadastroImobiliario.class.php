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
    * Classe de mapeamento para relatorio de Cadastro de Imoveis
    * Data de Criação: 28/04/2005

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Marcelo B. Paulino

    * @package URBEM
    * @subpackage Mapeamento

    * $Id: FCIMRelatorioCadastroImobiliario.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.18
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

set_time_limit(0);

class FCIMRelatorioCadastroImobiliario extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
    public function FCIMRelatorioCadastroImobiliario()
    {
        parent::Persistente();
        $this->setTabela('imobiliario.fn_rl_cadastro_imobiliario');

        $this->setCampoCod('');
        $this->setComplementoChave('');

        $this->AddCampo( 'inscricao_municipal' ,'integer', true, '',false, false );
        $this->AddCampo( 'proprietario_cota'   ,'varchar', true, '',false, false );
        $this->AddCampo( 'dt_cadastro'         ,'date'   , true, '',false, false );
        $this->AddCampo( 'cod_lote'            ,'integer', true, '',false, false );
        $this->AddCampo( 'tipo_lote'           ,'varchar', true, '',false, false );
        $this->AddCampo( 'valor_lote'          ,'varchar', true, '',false, false );
        $this->AddCampo( 'endereco'            ,'varchar', true, '',false, false );
        $this->AddCampo( 'cep'                 ,'varchar', true, '',false, false );
        $this->AddCampo( 'cod_localizacao'     ,'integer', true, '',false, false );
        $this->AddCampo( 'localizacao'         ,'varchar', true, '',false, false );
        $this->AddCampo( 'cod_condominio'      ,'integer', true, '',false, false );
        $this->AddCampo( 'creci'               ,'integer', true, '',false, false );
        $this->AddCampo( 'nom_bairro'          ,'varchar', true, '',false, false );
        $this->AddCampo( 'logradouro'          ,'varchar', true, '',false, false );
        $this->AddCampo( 'situacao'            ,'varchar', true, '',false, false );
        $this->AddCampo( 'stFiltroLote'        ,'varchar', true, '',false, false );
        $this->AddCampo( 'stFiltroImovel'      ,'varchar', true, '',false, false );
        $this->AddCampo( 'stDistinct'          ,'varchar', true, '',false, false );
        $this->AddCampo( 'stFiltroAtrbImovel'  ,'varchar', true, '',false, false );
        $this->AddCampo( 'stFiltroAtrbLote'    ,'varchar', true, '',false, false );
        $this->AddCampo( 'stFiltroAtrbEdf'     ,'varchar', true, '',false, false );
        $this->addCampo( 'stTipoSituacao'      ,'varchar', true, '',false, false );
    }

    public function montaRecuperaTodos()
    {
        $stSql  = "SELECT *                                                              \n";
        $stSql .= "FROM ".$this->getTabela()."( '".$this->getDado('stFiltroLote')."'     \n";
        $stSql .= "                            ,'".$this->getDado('stFiltroImovel')."'   \n";
        $stSql .= "                            ,'".$this->getDado('stDistinct')."'       \n";
        $stSql .= "                            ,'".$this->getDado('stFiltroAtrbImovel')."'\n";
        $stSql .= "                            ,'".$this->getDado('stFiltroAtrbLote')."' \n";
        $stSql .= "                            ,'".$this->getDado('stFiltroAtrbEdf')."'  \n";
        $stSql .= ") as retorno( inscricao_municipal  integer                            \n";
        $stSql .= "             ,proprietario_cota    text                               \n";
        $stSql .= "             ,cod_lote             integer                            \n";
        $stSql .= "             ,dt_cadastro          date                               \n";
        $stSql .= "             ,tipo_lote            text                               \n";
        $stSql .= "             ,valor_lote           varchar                            \n";
        $stSql .= "             ,endereco             varchar                            \n";
        $stSql .= "             ,cep                  varchar                            \n";
        $stSql .= "             ,cod_localizacao      integer                            \n";
        $stSql .= "             ,localizacao          text                               \n";
        $stSql .= "             ,cod_condominio       integer                            \n";
        $stSql .= "             ,creci                varchar                            \n";
        $stSql .= "             ,nom_bairro           varchar                            \n";
        $stSql .= "             ,logradouro           text                               \n";
        $stSql .= "             ,situacao             text                               \n";
        $stSql .= ")                                                                     \n";

        if (($this->getDado('stTipoSituacao') != 'todos' ) && ($this->getDado('stTipoSituacao'))) {
            $stSql .= " WHERE situacao = '".$this->getDado('stTipoSituacao')."'";
        }

        return $stSql;
    }

    public function recuperaBoletimCadastroImobiliario(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;

        $stSql = $this->montaRecuperaBoletimCadastroImobiliario().$stCondicao.$stOrdem;

        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaBoletimCadastroImobiliario()
    {
        $stSql  = " SELECT                                                                                                  \r";
        $stSql .= "     ii.inscricao_municipal,                                                                             \r";
        $stSql .= "     ip.numcgm                               AS numcgm_proprietario,                                     \r";
        $stSql .= "     ip.cota                                 AS cota_proprietario,                                       \r";
        $stSql .= "     cgm.nom_cgm                             as nom_cgm_proprietario,                                    \r";
        $stSql .= "     cgm.logradouro                          as logradouro_proprietario,                                 \r";
        $stSql .= "     (                                                                                                   \r";
        $stSql .= "      CASE                                                                                               \r";
        $stSql .= "         WHEN cgm.complemento != ''                                                                      \r";
        $stSql .= "         THEN cgm.numero||' - '||cgm.complemento                                                         \r";
        $stSql .= "         ELSE cgm.numero                                                                                 \r";
        $stSql .= "      END                                                                                                \r";
        $stSql .= "     )                                       as numero_proprietario,                                     \r";
        $stSql .= "     cgm.bairro as bairro_proprietario,                                                                  \r";
        $stSql .= "     (                                                                                                   \r";
        $stSql .= "     CASE                                                                                                \r";
        $stSql .= "         WHEN scpf.numcgm IS NOT NULL                                                                    \r";
        $stSql .= "         THEN scpf.cpf                                                                                   \r";
        $stSql .= "         ELSE scpj.cnpj                                                                                  \r";
        $stSql .= "     END                                                                                                 \r";
        $stSql .= "     )                                       as cpf_cnpj_proprietario,                                   \r";
        $stSql .= "     (                                                                                                   \r";
        $stSql .= "     CASE                                                                                                \r";
        $stSql .= "         WHEN scpf.numcgm IS NOT NULL                                                                    \r";
        $stSql .= "         THEN scpf.rg                                                                                    \r";
        $stSql .= "         ELSE scpj.insc_estadual                                                                         \r";
        $stSql .= "     END                                                                                                 \r";
        $stSql .= "     )                                       as rg_insc_estad_proprietario,                              \r";
        $stSql .= "     cgm_prom.bairro                         AS promitente_bairro,                                       \r";
        $stSql .= "     cgm_prom.logradouro                     AS promitente_logradouro,                                   \r";
        $stSql .= "     (                                                                                                   \r";
        $stSql .= "         CASE                                                                                            \r";
        $stSql .= "             WHEN cgm_prom.complemento IS NOT NULL                                                       \r";
        $stSql .= "             THEN cgm_prom.numero||' - '||cgm_prom.complemento                                           \r";
        $stSql .= "             ELSE cgm_prom.numero                                                                        \r";
        $stSql .= "         END                                                                                             \r";
        $stSql .= "     )                                       AS promitente_numero,                                       \r";
        $stSql .= "     cgm_prom.nom_cgm                        AS promitente_nome,                                         \r";
        $stSql .= "     ipromt.cota                             AS promitente_cota,                                         \r";
        $stSql .= "     ipromt.numcgm                           AS promitente_cgm,                                          \r";
        $stSql .= "     (                                                                                                   \r";
        $stSql .= "         CASE                                                                                            \r";
        $stSql .= "             WHEN scpf_prom.numcgm IS NOT NULL                                                           \r";
        $stSql .= "             THEN scpf_prom.cpf                                                                          \r";
        $stSql .= "             ELSE scpj_prom.cnpj                                                                         \r";
        $stSql .= "         END                                                                                             \r";
        $stSql .= "     )                                       AS promitente_cpf_cnpj,                                     \r";
        $stSql .= "     (                                                                                                   \r";
        $stSql .= "         CASE                                                                                            \r";
        $stSql .= "             WHEN scpf_prom.numcgm IS NOT NULL                                                           \r";
        $stSql .= "             THEN scpf_prom.rg                                                                           \r";
        $stSql .= "             ELSE scpj_prom.insc_estadual                                                                \r";
        $stSql .= "         END                                                                                             \r";
        $stSql .= "     )                                       AS promitente_rg_insc_estad,                                \r";
        $stSql .= "     bairro.nom_bairro                       AS imovel_bairro,                                           \r";
        $stSql .= "     il.nom_localizacao                      AS imovel_localizacao,                                      \r";
        $stSql .= "     (                                                                                                   \r";
        $stSql .= "     CASE                                                                                                \r";
        $stSql .= "         WHEN ii.complemento != ''                                                                       \r";
        $stSql .= "         THEN ii.numero||' - '||ii.complemento                                                           \r";
        $stSql .= "         ELSE ii.numero                                                                                  \r";
        $stSql .= "     END                                                                                                 \r";
        $stSql .= "     )                                       AS imovel_numero,                                           \r";
        $stSql .= "     ( SELECT                                                                                            \r";
        $stSql .= "            logra[2]                                                                                     \r";
        $stSql .= "       FROM                                                                                              \r";
        $stSql .= "            imobiliario.fn_consulta_logradouro( ii.inscricao_municipal ) AS logra                        \r";
        $stSql .= "     )                                       AS imovel_logradouro,                                       \r";
        $stSql .= "     (                                                                                                   \r";
        $stSql .= "         SELECT                                                                                          \r";
        $stSql .= "             CASE                                                                                        \r";
        $stSql .= "                 WHEN iip.cod_processo IS NOT NULL                                                       \r";
        $stSql .= "                 THEN iip.cod_processo||'/'||iip.ano_exercicio                                           \r";
        $stSql .= "                 ELSE ''                                                                                 \r";
        $stSql .= "             END                                                                                         \r";
        $stSql .= "         FROM                                                                                            \r";
        $stSql .= "             imobiliario.imovel_processo     AS iip,                                                     \r";
        $stSql .= "             (                                                                                           \r";
        $stSql .= "                 SELECT                                                                                  \r";
        $stSql .= "                     MAX(imovel_processo.timestamp) AS timestamp                                         \r";
        $stSql .= "                 FROM                                                                                    \r";
        $stSql .= "                     imobiliario.imovel_processo                                                         \r";
        $stSql .= "                 WHERE                                                                                   \r";
        $stSql .= "                     imovel_processo.inscricao_municipal = ii.inscricao_municipal                        \r";
        $stSql .= "             )                               AS iip_max                                                  \r";
        $stSql .= "         WHERE                                                                                           \r";
        $stSql .= "             iip.inscricao_municipal = ii.inscricao_municipal                                            \r";
        $stSql .= "             AND iip.timestamp = iip_max.timestamp                                                       \r";
        $stSql .= "      )                                      as imovel_processo,                                         \r";
        $stSql .= "     imobiliario.fn_calcula_area_imovel_lote( ii.inscricao_municipal ) AS area_lote,                     \r";
        $stSql .= "     imobiliario.fn_calcula_area_imovel( ii.inscricao_municipal ) AS area_total_imovel,                  \r";
        $stSql .= "     (                                                                                                   \r";
        $stSql .= "         SELECT                                                                                          \r";
        $stSql .= "             (                                                                                           \r";
        $stSql .= "                 SELECT                                                                                  \r";
        $stSql .= "                     ic.nom_condominio                                                                   \r";
        $stSql .= "                 FROM                                                                                    \r";
        $stSql .= "                     imobiliario.condominio AS ic                                                        \r";
        $stSql .= "                 WHERE                                                                                   \r";
        $stSql .= "                     iic.cod_condominio = ic.cod_condominio                                              \r";
        $stSql .= "             )                               AS nom_condominio                                           \r";
        $stSql .= "         FROM                                                                                            \r";
        $stSql .= "             imobiliario.imovel_condominio   AS iic                                                      \r";
        $stSql .= "         WHERE                                                                                           \r";
        $stSql .= "             iic.inscricao_municipal = ii.inscricao_municipal                                            \r";
        $stSql .= "     )                                       AS condominio,                                              \r";
        $stSql .= "     to_char( ii.dt_cadastro, 'dd/mm/yyyy' ) AS data_inscricao,                                          \r";
        $stSql .= "     (                                                                                                   \r";
        $stSql .= "         SELECT                                                                                          \r";
        $stSql .= "             imi.mat_registro_imovel                                                                     \r";
        $stSql .= "         FROM                                                                                            \r";
        $stSql .= "             imobiliario.matricula_imovel    AS imi,                                                     \r";
        $stSql .= "             (                                                                                           \r";
        $stSql .= "                 SELECT                                                                                  \r";
        $stSql .= "                     MAX(timestamp)          as timestamp                                                \r";
        $stSql .= "                 FROM                                                                                    \r";
        $stSql .= "                     imobiliario.matricula_imovel                                                        \r";
        $stSql .= "                 WHERE                                                                                   \r";
        $stSql .= "                     inscricao_municipal = ii.inscricao_municipal                                        \r";
        $stSql .= "             )                               AS mi                                                       \r";
        $stSql .= "         WHERE                                                                                           \r";
        $stSql .= "             imi.timestamp = mi.timestamp AND                                                            \r";
        $stSql .= "             imi.inscricao_municipal = ii.inscricao_municipal                                            \r";
        $stSql .= "     )                                       AS matricula_imovel,                                        \r";
        $stSql .= "     (                                                                                                   \r";
        $stSql .= "         SELECT                                                                                          \r";
        $stSql .= "             ipm.vl_profundidade_media                                                                   \r";
        $stSql .= "         FROM                                                                                            \r";
        $stSql .= "             imobiliario.lote                AS il                                                       \r";
        $stSql .= "         INNER JOIN                                                                                      \r";
        $stSql .= "             imobiliario.profundidade_media  AS ipm                                                      \r";
        $stSql .= "         ON                                                                                              \r";
        $stSql .= "             il.timestamp = ipm.timestamp AND                                                            \r";
        $stSql .= "             il.cod_lote = ipm.cod_lote                                                                  \r";
        $stSql .= "         WHERE                                                                                           \r";
        $stSql .= "             il.cod_lote = iil.cod_lote                                                                  \r";
        $stSql .= "     )                                       AS profundidade_imovel,                                     \r";
        $stSql .= "     (                                                                                                   \r";
        $stSql .= "         SELECT                                                                                          \r";
        $stSql .= "             CASE                                                                                        \r";
        $stSql .= "                 WHEN il.cod_loteamento IS NOT NULL                                                      \r";
        $stSql .= "                 THEN il.cod_loteamento||' - '||il.nom_loteamento                                        \r";
        $stSql .= "             END                             AS loteamento                                               \r";
        $stSql .= "         FROM                                                                                            \r";
        $stSql .= "             imobiliario.lote_loteamento     AS ill                                                      \r";
        $stSql .= "         INNER JOIN                                                                                      \r";
        $stSql .= "             imobiliario.loteamento          AS il                                                       \r";
        $stSql .= "         ON                                                                                              \r";
        $stSql .= "             il.cod_loteamento = ill.cod_loteamento                                                      \r";
        $stSql .= "         WHERE                                                                                           \r";
        $stSql .= "             ill.cod_lote = iil.cod_lote                                                                 \r";
        $stSql .= "     )                                       AS loteamento_imovel,                                       \r";
        $stSql .= "     (                                                                                                   \r";
        $stSql .= "         SELECT                                                                                          \r";
        $stSql .= "             iii.creci                                                                                   \r";
        $stSql .= "         FROM                                                                                            \r";
        $stSql .= "             imobiliario.imovel_imobiliaria  AS iii,                                                     \r";
        $stSql .= "             (                                                                                           \r";
        $stSql .= "                 SELECT                                                                                  \r";
        $stSql .= "                     MAX(timestamp)          AS timestamp                                                \r";
        $stSql .= "                 FROM                                                                                    \r";
        $stSql .= "                     imobiliario.imovel_imobiliaria                                                      \r";
        $stSql .= "                 WHERE                                                                                   \r";
        $stSql .= "                     inscricao_municipal = ii.inscricao_municipal                                        \r";
        $stSql .= "             )                               AS iit                                                      \r";
        $stSql .= "         WHERE                                                                                           \r";
        $stSql .= "             iit.timestamp = iii.timestamp AND                                                           \r";
        $stSql .= "             iii.inscricao_municipal = ii.inscricao_municipal                                            \r";
        $stSql .= "     )                                       AS corretagem_imovel,                                       \r";
        $stSql .= "     (                                                                                                   \r";
        $stSql .= "         SELECT                                                                                          \r";
        $stSql .= "             ipc.nom_ponto                                                                               \r";
        $stSql .= "         FROM                                                                                            \r";
        $stSql .= "             imobiliario.ponto_cardeal       AS ipc                                                      \r";
        $stSql .= "         WHERE                                                                                           \r";
        $stSql .= "             ipc.cod_ponto = ic.cod_ponto                                                                \r";
        $stSql .= "     )                                       AS conf_lot_ponto_cardeal,                                  \r";
        $stSql .= "     (                                                                                                   \r";
        $stSql .= "         SELECT                                                                                          \r";
        $stSql .= "             ice.valor                                                                                   \r";
        $stSql .= "         FROM                                                                                            \r";
        $stSql .= "             imobiliario.confrontacao_extensao AS ice,                                                   \r";
        $stSql .= "             (                                                                                           \r";
        $stSql .= "                 SELECT                                                                                  \r";
        $stSql .= "                     MAX(timestamp)          AS timestamp                                                \r";
        $stSql .= "                 FROM                                                                                    \r";
        $stSql .= "                     imobiliario.confrontacao_extensao                                                   \r";
        $stSql .= "                 WHERE                                                                                   \r";
        $stSql .= "                     cod_lote = iil.cod_lote AND                                                         \r";
        $stSql .= "                     cod_confrontacao = ic.cod_confrontacao                                              \r";
        $stSql .= "             )                               AS ice2                                                     \r";
        $stSql .= "         WHERE                                                                                           \r";
        $stSql .= "             ice.timestamp = ice2.timestamp AND                                                          \r";
        $stSql .= "             ice.cod_lote = iil.cod_lote AND                                                             \r";
        $stSql .= "             ice.cod_confrontacao = ic.cod_confrontacao                                                  \r";
        $stSql .= "     )                                       AS conf_lot_metragem,                                       \r";
        $stSql .= "     (                                                                                                   \r";
        $stSql .= "         SELECT                                                                                          \r";
        $stSql .= "             CASE                                                                                        \r";
        $stSql .= "                 WHEN ictre.cod_trecho IS NOT NULL                                                       \r";
        $stSql .= "                 THEN ictre.cod_trecho::text                                                             \r";
        $stSql .= "                 ELSE                                                                                    \r";
        $stSql .= "                     CASE                                                                                \r";
        $stSql .= "                         WHEN icd.descricao IS NOT NULL                                                  \r";
        $stSql .= "                         THEN icd.descricao::text                                                        \r";
        $stSql .= "                         ELSE icl.cod_lote_confrontacao::text                                            \r";
        $stSql .= "                     END                                                                                 \r";
        $stSql .= "             END                                                                                         \r";
        $stSql .= "         FROM                                                                                            \r";
        $stSql .= "             imobiliario.confrontacao        AS ict                                                      \r";
        $stSql .= "         LEFT JOIN                                                                                       \r";
        $stSql .= "             imobiliario.confrontacao_trecho AS ictre                                                    \r";
        $stSql .= "         ON                                                                                              \r";
        $stSql .= "             ictre.cod_lote = ic.cod_lote AND                                                            \r";
        $stSql .= "             ictre.cod_confrontacao = ic.cod_confrontacao                                                \r";
        $stSql .= "         LEFT JOIN                                                                                       \r";
        $stSql .= "             imobiliario.confrontacao_diversa AS icd                                                     \r";
        $stSql .= "         ON                                                                                              \r";
        $stSql .= "             icd.cod_lote = ic.cod_lote AND                                                              \r";
        $stSql .= "             icd.cod_confrontacao = ic.cod_confrontacao                                                  \r";
        $stSql .= "         LEFT JOIN                                                                                       \r";
        $stSql .= "             imobiliario.confrontacao_lote   AS icl                                                      \r";
        $stSql .= "         ON                                                                                              \r";
        $stSql .= "             icl.cod_lote = ic.cod_lote AND                                                              \r";
        $stSql .= "             icl.cod_confrontacao = ic.cod_confrontacao                                                  \r";
        $stSql .= "         WHERE                                                                                           \r";
        $stSql .= "             ict.cod_lote = ic.cod_lote AND                                                              \r";
        $stSql .= "             ict.cod_confrontacao = ic.cod_confrontacao                                                  \r";
        $stSql .= "     )                                       AS conf_lot_especificar,                                    \r";
        $stSql .= "     (                                                                                                   \r";
        $stSql .= "         SELECT                                                                                          \r";
        $stSql .= "             CASE                                                                                        \r";
        $stSql .= "                 WHEN ict.principal = 't'                                                                \r";
        $stSql .= "                 THEN 'Principal'                                                                        \r";
        $stSql .= "                 ELSE 'Não principal'                                                                    \r";
        $stSql .= "             END                                                                                         \r";
        $stSql .= "         FROM                                                                                            \r";
        $stSql .= "             imobiliario.confrontacao        AS iconf                                                    \r";
        $stSql .= "         LEFT JOIN                                                                                       \r";
        $stSql .= "             imobiliario.confrontacao_trecho AS ict                                                      \r";
        $stSql .= "         ON                                                                                              \r";
        $stSql .= "             ict.cod_confrontacao = ic.cod_confrontacao AND                                              \r";
        $stSql .= "             ict.cod_lote = ic.cod_lote                                                                  \r";
        $stSql .= "         WHERE                                                                                           \r";
        $stSql .= "             iconf.cod_confrontacao = ic.cod_confrontacao AND                                            \r";
        $stSql .= "             iconf.cod_lote = ic.cod_lote                                                                \r";
        $stSql .= "     )                                       AS conf_principal,                                          \r";
        $stSql .= "     iil.cod_lote,                                                                                       \r";
        $stSql .= "     unidade.tipo_vinculo,                                                                               \r";
        $stSql .= "     unidade.cod_construcao,                                                                             \r";
        $stSql .= "     unidade.cod_tipo,                                                                                   \r";
        $stSql .= "     unidade.area AS area_da_unidade,                                                                    \r";
        $stSql .= "     to_char(data_construcao.data_construcao,'dd/mm/yyyy' ) AS data_construcao                           \r";
        $stSql .= " FROM                                                                                                    \r";
        $stSql .= "     imobiliario.imovel                      AS ii                                                       \r";
        $stSql .= " INNER JOIN                                                                                              \r";
        $stSql .= "     (                                                                                                   \r";
        $stSql .= "         SELECT                                                                                          \r";
        $stSql .= "             ii.inscricao_municipal,                                                                     \r";
        $stSql .= "             CASE                                                                                        \r";
        $stSql .= "                 WHEN iua.inscricao_municipal IS NOT NULL                                                \r";
        $stSql .= "                 THEN 'Autônoma'                                                                         \r";
        $stSql .= "                 ELSE 'Dependente'                                                                       \r";
        $stSql .= "             END                             AS tipo_vinculo,                                            \r";
        $stSql .= "             CASE                                                                                        \r";
        $stSql .= "                 WHEN iua.inscricao_municipal IS NOT NULL                                                \r";
        $stSql .= "                 THEN                                                                                    \r";
        $stSql .= "                     imobiliario.fn_area_unidade_autonoma(iua.cod_construcao, iua.inscricao_municipal)   \r";
        $stSql .= "                 ELSE                                                                                    \r";
        $stSql .= "                     imobiliario.fn_area_unidade_dependente(iud.cod_construcao, iud.inscricao_municipal) \r";
        $stSql .= "             END                             AS area,                                                    \r";
        $stSql .= "             COALESCE(iua.cod_construcao, iud.cod_construcao) AS cod_construcao,                         \r";
        $stSql .= "             COALESCE(iua.cod_tipo, iud.cod_tipo) AS cod_tipo                                            \r";
        $stSql .= "         FROM                                                                                            \r";
        $stSql .= "             imobiliario.imovel              AS ii                                                       \r";
        $stSql .= "         LEFT JOIN                                                                                       \r";
        $stSql .= "             imobiliario.unidade_autonoma    AS iua                                                      \r";
        $stSql .= "         ON                                                                                              \r";
        $stSql .= "             iua.inscricao_municipal = ii.inscricao_municipal                                            \r";
        $stSql .= "         LEFT JOIN                                                                                       \r";
        $stSql .= "             imobiliario.unidade_dependente  AS iud                                                      \r";
        $stSql .= "         ON                                                                                              \r";
        $stSql .= "             iud.inscricao_municipal = ii.inscricao_municipal                                            \r";
        $stSql .= "     )                                       AS unidade                                                  \r";
        $stSql .= " ON                                                                                                      \r";
        $stSql .= "     unidade.inscricao_municipal = ii.inscricao_municipal                                                \r";
        $stSql .= " LEFT JOIN                                                                                               \r";
        $stSql .= "     imobiliario.data_construcao                                                                         \r";
        $stSql .= " ON                                                                                                      \r";
        $stSql .= "     unidade.cod_construcao = data_construcao.cod_construcao                                             \r";
        $stSql .= " LEFT JOIN imobiliario.proprietario          AS ipromt                                                   \r";
        $stSql .= "     ON ipromt.promitente = 't' AND ipromt.inscricao_municipal = ii.inscricao_municipal                  \r";
        $stSql .= " LEFT JOIN                                                                                               \r";
        $stSql .= "     sw_cgm as cgm_prom                                                                                  \r";
        $stSql .= " ON                                                                                                      \r";
        $stSql .= "     cgm_prom.numcgm = ipromt.numcgm                                                                     \r";
        $stSql .= " LEFT JOIN                                                                                               \r";
        $stSql .= "     sw_cgm_pessoa_fisica as scpf_prom                                                                   \r";
        $stSql .= " ON                                                                                                      \r";
        $stSql .= "     scpf_prom.numcgm = ipromt.numcgm                                                                    \r";
        $stSql .= " LEFT JOIN                                                                                               \r";
        $stSql .= "     sw_cgm_pessoa_juridica as scpj_prom                                                                 \r";
        $stSql .= " ON                                                                                                      \r";
        $stSql .= "     scpj_prom.numcgm = ipromt.numcgm                                                                    \r";
        $stSql .= " INNER JOIN (                                                                                            \r";
        $stSql .= "     SELECT                                                                                              \r";
        $stSql .= "         ip.*                                                                                            \r";
        $stSql .= "     FROM                                                                                                \r";
        $stSql .= "         imobiliario.proprietario            AS ip,                                                      \r";
        $stSql .= "         (                                                                                               \r";
        $stSql .= "             SELECT                                                                                      \r";
        $stSql .= "                 MAX ( ip.cota )             AS cota,                                                    \r";
        $stSql .= "                 ip.inscricao_municipal                                                                  \r";
        $stSql .= "             FROM                                                                                        \r";
        $stSql .= "                 imobiliario.proprietario    AS ip                                                       \r";
        $stSql .= "             WHERE                                                                                       \r";
        $stSql .= "                 ip.promitente = 'f'                                                                     \r";
        $stSql .= "             GROUP BY                                                                                    \r";
        $stSql .= "                 ip.inscricao_municipal                                                                  \r";
        $stSql .= "         )AS ipp                                                                                         \r";
        $stSql .= "     WHERE                                                                                               \r";
        $stSql .= "         ipp.inscricao_municipal = ip.inscricao_municipal AND                                            \r";
        $stSql .= "         ip.promitente = 'f' AND                                                                         \r";
        $stSql .= "         ipp.cota = ip.cota                                                                              \r";
        $stSql .= " )                                           AS ip                                                       \r";
        $stSql .= " ON                                                                                                      \r";
        $stSql .= "     ip.inscricao_municipal = ii.inscricao_municipal                                                     \r";
        $stSql .= " INNER JOIN                                                                                              \r";
        $stSql .= "     imobiliario.imovel_lote                 AS iil                                                      \r";
        $stSql .= " ON                                                                                                      \r";
        $stSql .= "     iil.inscricao_municipal = ii.inscricao_municipal                                                    \r";
        $stSql .= " INNER JOIN                                                                                              \r";
        $stSql .= "     imobiliario.lote_localizacao            AS ill                                                      \r";
        $stSql .= " ON                                                                                                      \r";
        $stSql .= "     ill.cod_lote = iil.cod_lote                                                                         \r";
        $stSql .= " INNER JOIN                                                                                              \r";
        $stSql .= "     imobiliario.imovel_confrontacao         AS iiconf                                                   \r";
        $stSql .= " ON                                                                                                      \r";
        $stSql .= "     iiconf.inscricao_municipal = ii.inscricao_municipal                                                 \r";
        $stSql .= " INNER JOIN                                                                                              \r";
        $stSql .= "     imobiliario.confrontacao_trecho         AS iconftre                                                 \r";
        $stSql .= " ON                                                                                                      \r";
        $stSql .= "     iconftre.cod_confrontacao = iiconf.cod_confrontacao AND                                             \r";
        $stSql .= "     iconftre.cod_lote = iiconf.cod_lote                                                                 \r";
        $stSql .= " INNER JOIN                                                                                              \r";
        $stSql .= "     imobiliario.confrontacao                AS ic                                                       \r";
        $stSql .= " ON                                                                                                      \r";
        $stSql .= "     ic.cod_lote = iiconf.cod_lote AND                                                                   \r";
        $stSql .= "     ic.cod_confrontacao = iiconf.cod_confrontacao                                                       \r";
        $stSql .= " INNER JOIN                                                                                              \r";
        $stSql .= "     imobiliario.localizacao                 AS il                                                       \r";
        $stSql .= " ON                                                                                                      \r";
        $stSql .= "     il.cod_localizacao = ill.cod_localizacao                                                            \r";
        $stSql .= " INNER JOIN sw_cgm as cgm ON cgm.numcgm = ip.numcgm                                                      \r";
        $stSql .= " LEFT JOIN sw_cgm_pessoa_fisica as scpf ON scpf.numcgm = ip.numcgm                                       \r";
        $stSql .= " LEFT JOIN sw_cgm_pessoa_juridica as scpj ON scpj.numcgm = ip.numcgm                                     \r";
        $stSql .= " INNER JOIN                                                                                              \r";
        $stSql .= "     (   select ilb.cod_lote, bairro.cod_bairro, bairro.nom_bairro                                       \r";
        $stSql .= "         from sw_bairro as bairro                                                                        \r";
        $stSql .= "         INNER JOIN imobiliario.lote_bairro as ilb                                                       \r";
        $stSql .= "             ON ilb.cod_bairro = bairro.cod_bairro                                                       \r";
        $stSql .= "             AND bairro.cod_uf = ilb.cod_uf                                                              \r";
        $stSql .= "             AND bairro.cod_municipio = ilb.cod_municipio                                                \r";
        $stSql .= "     ) as bairro ON bairro.cod_lote = ill.cod_lote                                                       \r";

        return $stSql;
    }

    public function recuperaCaracteristicasTerreno(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;

        $stSql = $this->montaRecuperaCaracteristicasTerreno().$stCondicao.$stOrdem;

        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaCaracteristicasTerreno()
    {
        $stSql  = " SELECT                                                                                      \r";
        $stSql .= "     coalesce( ilr.cod_atributo, ilu.cod_atributo) AS cod_atr_din_lote,                      \r";
        $stSql .= "     coalesce( ilr.valor, ilu.valor) AS valor_atr_din_lote                                   \r";
        $stSql .= " FROM                                                                                        \r";
        $stSql .= "     imobiliario.lote AS il                                                                  \r";
        $stSql .= " LEFT JOIN (                                                                                 \r";
        $stSql .= "     SELECT                                                                                  \r";
        $stSql .= "         ial.*                                                                               \r";
        $stSql .= "     FROM                                                                                    \r";
        $stSql .= "         imobiliario.lote_rural AS ilr                                                       \r";
        $stSql .= "     INNER JOIN                                                                              \r";
        $stSql .= "         imobiliario.atributo_lote_rural_valor AS ial                                        \r";
        $stSql .= "     ON                                                                                      \r";
        $stSql .= "         ilr.cod_lote = ial.cod_lote                                                         \r";
        $stSql .= " ) AS ilr                                                                                    \r";
        $stSql .= " ON                                                                                          \r";
        $stSql .= "     ilr.cod_lote = il.cod_lote                                                              \r";
        $stSql .= " LEFT JOIN (                                                                                 \r";
        $stSql .= "     SELECT                                                                                  \r";
        $stSql .= "         ial.*                                                                               \r";
        $stSql .= "     FROM                                                                                    \r";
        $stSql .= "         imobiliario.lote_urbano AS ilr                                                      \r";
        $stSql .= "     INNER JOIN                                                                              \r";
        $stSql .= "         imobiliario.atributo_lote_urbano_valor AS ial                                       \r";
        $stSql .= "     ON                                                                                      \r";
        $stSql .= "         ilr.cod_lote = ial.cod_lote                                                         \r";
        $stSql .= " ) AS ilu                                                                                    \r";
        $stSql .= " ON                                                                                          \r";
        $stSql .= "     ilu.cod_lote = il.cod_lote                                                              \r";

        return $stSql;
    }

    public function recuperaCaracteristicasImovel(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;

        $stSql = $this->montaRecuperaCaracteristicasImovel().$stCondicao.$stOrdem;

        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaCaracteristicasImovel()
    {
        $stSql  = " SELECT                                              \r";
        $stSql .= "     iav.cod_atributo,                               \r";
        $stSql .= "     iav.valor                                       \r";
        $stSql .= " FROM                                                \r";
        $stSql .= "     imobiliario.atributo_imovel_valor AS iav        \r";

        return $stSql;
    }

    public function recuperaCaracteristicasEdificacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;

        $stSql = $this->montaRecuperaCaracteristicasEdificacao().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaCaracteristicasEdificacao()
    {
        $stSql  = " SELECT                                                                                  \r";
        $stSql .= "     ic.cod_construcao,                                                                  \r";
        $stSql .= "     COALESCE( ice.cod_modulo, ico.cod_modulo, icc.cod_modulo) AS cod_modulo,            \r";
        $stSql .= "     COALESCE( ice.cod_atributo, ico.cod_atributo, icc.cod_atributo) AS cod_atributo,    \r";
        $stSql .= "     COALESCE( ice.cod_cadastro, ico.cod_cadastro, icc.cod_cadastro) AS cod_cadastro,    \r";
        $stSql .= "     COALESCE( ice.valor, ico.valor, icc.valor) AS valor                                 \r";
        $stSql .= " FROM                                                                                    \r";
        $stSql .= "     imobiliario.construcao AS ic                                                        \r";
        $stSql .= " LEFT JOIN                                                                               \r";
        $stSql .= "     (                                                                                   \r";
        $stSql .= "         SELECT                                                                          \r";
        $stSql .= "             ice.cod_construcao,                                                         \r";
        $stSql .= "             ice.cod_tipo,                                                               \r";
        $stSql .= "             iat.cod_cadastro,                                                           \r";
        $stSql .= "             iat.cod_atributo,                                                           \r";
        $stSql .= "             iat.cod_modulo,                                                             \r";
        $stSql .= "             (                                                                           \r";
        $stSql .= "                 SELECT                                                                  \r";
        $stSql .= "                     iatv.valor                                                          \r";
        $stSql .= "                 FROM                                                                    \r";
        $stSql .= "                     imobiliario.atributo_tipo_edificacao_valor AS iatv                  \r";
        $stSql .= "                 WHERE                                                                   \r";
        $stSql .= "                     iatv.cod_modulo = iat.cod_modulo                                    \r";
        $stSql .= "                     AND iatv.cod_cadastro = iat.cod_cadastro                            \r";
        $stSql .= "                     AND iatv.cod_atributo = iat.cod_atributo                            \r";
        $stSql .= "                     AND iatv.cod_tipo = iat.cod_tipo                                    \r";
        $stSql .= "                     AND iatv.cod_construcao = ice.cod_construcao                        \r";
        $stSql .= "                 ORDER BY                                                                \r";
        $stSql .= "                    iatv.timestamp DESC                                                  \r";
        $stSql .= "                 LIMIT 1                                                                 \r";
        $stSql .= "             ) AS valor                                                                  \r";
        $stSql .= "         FROM                                                                            \r";
        $stSql .= "             imobiliario.construcao_edificacao AS ice                                    \r";
        $stSql .= "         INNER JOIN                                                                      \r";
        $stSql .= "             imobiliario.atributo_tipo_edificacao AS iat                                 \r";
        $stSql .= "         ON                                                                              \r";
        $stSql .= "             iat.cod_tipo = ice.cod_tipo                                                 \r";
        $stSql .= "             AND iat.ativo = 't'                                                         \r";
        $stSql .= "     )AS ice                                                                             \r";
        $stSql .= " ON                                                                                      \r";
        $stSql .= "     ice.cod_construcao = ic.cod_construcao                                              \r";
        $stSql .= " LEFT JOIN                                                                               \r";
        $stSql .= "     (                                                                                   \r";
        $stSql .= "         SELECT                                                                          \r";
        $stSql .= "             iac.*                                                                       \r";
        $stSql .= "         FROM                                                                            \r";
        $stSql .= "             imobiliario.construcao_outros AS ico                                        \r";
        $stSql .= "         INNER JOIN                                                                      \r";
        $stSql .= "             imobiliario.atributo_construcao_outros_valor AS iac                         \r";
        $stSql .= "         ON                                                                              \r";
        $stSql .= "             iac.cod_construcao = ico.cod_construcao                                     \r";
        $stSql .= "     )AS ico                                                                             \r";
        $stSql .= " ON                                                                                      \r";
        $stSql .= "     ico.cod_construcao = ic.cod_construcao                                              \r";
        $stSql .= " LEFT JOIN                                                                               \r";
        $stSql .= "     (                                                                                   \r";
        $stSql .= "         SELECT                                                                          \r";
        $stSql .= "             icc.cod_construcao,                                                         \r";
        $stSql .= "             iac.*                                                                       \r";
        $stSql .= "         FROM                                                                            \r";
        $stSql .= "             imobiliario.construcao_condominio AS icc                                    \r";
        $stSql .= "         INNER JOIN                                                                      \r";
        $stSql .= "             imobiliario.atributo_condominio_valor AS iac                                \r";
        $stSql .= "         ON                                                                              \r";
        $stSql .= "             iac.cod_condominio = icc.cod_condominio                                     \r";
        $stSql .= "     )AS icc                                                                             \r";
        $stSql .= " ON                                                                                      \r";
        $stSql .= "     icc.cod_construcao = ic.cod_construcao                                              \r";

        return $stSql;
    }

}
