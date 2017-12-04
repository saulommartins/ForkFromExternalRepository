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
  * Classe de mapeamento da tabela ARRECADACAO.CADASTRO_ECONOMICO_CALCULO
    * Data de Criação: 12/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TARRCadastroEconomicoCalculo.class.php 65602 2016-06-01 17:44:39Z evandro $

* Casos de uso: uc-05.03.05
*/

/*
$Log$
Revision 1.15  2007/08/07 20:24:44  cercato
Bug#9837#

Revision 1.14  2007/07/05 18:35:09  cercato
Bug #9571#

Revision 1.13  2007/07/05 13:23:57  cercato
Bug #9571#

Revision 1.12  2007/05/17 12:56:17  cercato
correcao na consulta da escrituracao para apresentar o endereco corretamente.

Revision 1.11  2006/12/22 16:38:18  cercato
alterando consulta da escrituracao para calcular juros e multa de parcelas reemitidas.

Revision 1.10  2006/12/18 18:28:00  cercato
correcao para apresentar valor correto no carne de iss.

Revision 1.9  2006/11/28 15:55:08  cercato
bug #7671#

Revision 1.8  2006/11/27 15:11:15  cercato
bug #7660#

Revision 1.7  2006/11/02 14:51:37  domluc
Carnes para Mata de São Joao

Revision 1.6  2006/10/26 14:08:14  cercato
alterando mapeando de acordo com modificacoes no Bd.

Revision 1.5  2006/09/15 11:50:01  fabio
corrigidas tags de caso de uso

Revision 1.4  2006/09/15 10:40:57  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ARRECADACAO.CADASTRO_ECONOMICO_CALCULO
  * Data de Criação: 18/05/2005

  * @author Analista: Fabio Bertoldi
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TARRCadastroEconomicoCalculo extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TARRCadastroEconomicoCalculo()
{
    parent::Persistente();
    $this->setTabela('arrecadacao.cadastro_economico_calculo');

    $this->setCampoCod('cod_calculo');
    $this->setComplementoChave('cod_calculo,inscricao_economico,timestamp');

    $this->AddCampo('cod_calculo','integer',true,'',true,true);
    $this->AddCampo('inscricao_economica','integer',true,'',true,true);
    $this->AddCampo('timestamp','timestamp',false,'',false,true);

}

function recuperaConsultaReqReceita(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "", $stDataCorrente = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = "";
    $stSql = $this->montaRecuperaConsultaReqReceita($stDataCorrente).$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaConsultaReqReceita($stDataCorrente)
{
    $stSql = "SELECT DISTINCT
    CASE WHEN (('".$stDataCorrente."' > ap.vencimento) OR ( '".$stDataCorrente."' > apr.vencimento)) THEN
        ap.valor +
        ( ( ac.valor * ( 100 / ap.valor ) ) * ( aplica_multa(acne.numeracao, acne.exercicio::int, acne.cod_parcela, '".$stDataCorrente."') ) / 100 ) +
        ( (  ac.valor * ( 100 / ap.valor ) ) * ( aplica_juro(acne.numeracao, acne.exercicio::int, acne.cod_parcela, '".$stDataCorrente."') ) / 100 )
    ELSE
        ap.valor
    END::numeric(14,2) AS valor_total,

    CASE WHEN (('".$stDataCorrente."' > ap.vencimento) OR ( '".$stDataCorrente."' > apr.vencimento)) THEN
        ( ac.valor * ( 100 / ap.valor ) ) * ( aplica_multa(acne.numeracao, acne.exercicio::int, acne.cod_parcela, '".$stDataCorrente."') ) / 100
    ELSE
        0.00
    END::numeric(14,2) AS valor_multa,

    CASE WHEN (('".$stDataCorrente."' > ap.vencimento) OR ( '".$stDataCorrente."' > apr.vencimento)) THEN
        ( ( ac.valor  ) * ( 100 / ap.valor ) ) * ( aplica_juro(acne.numeracao, acne.exercicio::int, acne.cod_parcela, '".$stDataCorrente."') ) / 100
    ELSE
        0.00
    END::numeric(14,2) AS valor_juro,

    CASE WHEN (('".$stDataCorrente."' > ap.vencimento) OR ( '".$stDataCorrente."' > apr.vencimento)) THEN
        ( ( ac.valor  ) * ( 100 / ap.valor ) ) * ( aplica_correcao(acne.numeracao, acne.exercicio::int, acne.cod_parcela, '".$stDataCorrente."') ) / 100
    ELSE
        0.00
    END::numeric(14,2) AS valor_correcao,

    al.observacao,
    acec.inscricao_economica,
    ac.exercicio,
    ap.vencimento-'1997-10-07'::date as fator_vencimento,
    to_char(ap.vencimento, 'dd/mm/yyyy' ) AS vencimento,
    ap.valor AS valor_pagamento,
    CASE WHEN ap.nr_parcela = 0 THEN
        'Única'::text
    ELSE
        nr_parcela::text
    END AS nro_parcela,
    (
        SELECT
            ea.nom_atividade
        FROM
            economico.atividade AS ea
        WHERE
            ea.cod_atividade = afs.cod_atividade
    )AS nom_atividade,
    (
        SELECT
            cgm.nom_cgm
        FROM
            sw_cgm AS cgm
        WHERE
              COALESCE ( CEED.numcgm, CEEF.numcgm, CEA.numcgm ) = cgm.numcgm
    )AS nom_cgm,
    (
        SELECT
            cgm_pj.nom_fantasia
        FROM
            sw_cgm_pessoa_juridica AS cgm_pj
        WHERE
            cgm_pj.numcgm = COALESCE ( CEED.numcgm, CEEF.numcgm, CEA.numcgm )
    )AS nom_fantasia,
    acef.timestamp,
    CASE WHEN (edf.inscricao_municipal IS NOT NULL) AND (edi.inscricao_economica IS NOT NULL) THEN
        CASE WHEN (edf.timestamp > edi.timestamp) THEN
            split_part ( economico.fn_busca_domicilio_fiscal( acef.inscricao_economica ), '§', 1)||' '||split_part ( economico.fn_busca_domicilio_fiscal( acef.inscricao_economica ), '§', 3)||', '||split_part ( economico.fn_busca_domicilio_fiscal( acef.inscricao_economica ), '§', 4)
        ELSE
            split_part ( economico.fn_busca_domicilio_informado( acef.inscricao_economica ), '§', 1)||' '||split_part ( economico.fn_busca_domicilio_informado( acef.inscricao_economica ), '§', 3)||', '||split_part ( economico.fn_busca_domicilio_informado( acef.inscricao_economica ), '§', 4)
        END
    ELSE
        CASE WHEN (edf.inscricao_municipal IS NOT NULL) THEN
            split_part ( economico.fn_busca_domicilio_fiscal( acef.inscricao_economica ), '§', 1)||' '||split_part ( economico.fn_busca_domicilio_fiscal( acef.inscricao_economica ), '§', 3)||', '||split_part ( economico.fn_busca_domicilio_fiscal( acef.inscricao_economica ), '§', 4)
        ELSE
            split_part ( economico.fn_busca_domicilio_informado( acef.inscricao_economica ), '§', 1)||' '||split_part ( economico.fn_busca_domicilio_informado( acef.inscricao_economica ), '§', 3)||', '||split_part ( economico.fn_busca_domicilio_informado( acef.inscricao_economica ), '§', 4)
        END
    END AS endereco,
    acne.numeracao,
    (
       arrecadacao.fn_busca_origem_lancamento_sem_exercicio( al.cod_lancamento, 1, 1)
    )AS nom_grupo_credito,
    acef.competencia,
    COALESCE ( afs.cod_servico, ars.cod_servico ) AS cod_servico,
    afs.cod_atividade,
    COALESCE( ana.nro_nota, anf.nro_nota, arn.num_nota ) AS nro_nota,
    (
        SELECT
            numcgm||' - '||nom_cgm
        FROM
            sw_cgm
        WHERE
            sw_cgm.numcgm = coalesce( arn.numcgm_retentor, ana.numcgm_tomador )
    )AS prestador,
    (
        SELECT
            sum ( servico_sem_retencao.valor_declarado )
        FROM
            arrecadacao.servico_sem_retencao

        WHERE
            servico_sem_retencao.inscricao_economica = acef.inscricao_economica
            AND servico_sem_retencao.timestamp = acef.timestamp
    )AS valor_declarado

FROM
    economico.cadastro_economico AS ece

LEFT JOIN
    arrecadacao.cadastro_economico_faturamento AS acef
ON
    acef.inscricao_economica = ece.inscricao_economica

LEFT JOIN
    economico.cadastro_economico_empresa_direito CEED
ON
    CEED.inscricao_economica = acef.inscricao_economica

LEFT JOIN
    economico.cadastro_economico_empresa_fato CEEF
ON
    CEEF.inscricao_economica = acef.inscricao_economica

LEFT JOIN
    economico.cadastro_economico_autonomo CEA
ON
    CEA.inscricao_economica = acef.inscricao_economica

LEFT JOIN (
    SELECT
        edf_tmp.inscricao_economica,
        edf_tmp.inscricao_municipal,
        edf_tmp.timestamp
    FROM
        economico.domicilio_fiscal AS edf_tmp,
        (
            SELECT
                MAX (timestamp) AS timestamp,
                inscricao_economica
            FROM
                economico.domicilio_fiscal
            GROUP BY
                inscricao_economica
        )AS tmp
    WHERE
        tmp.timestamp = edf_tmp.timestamp
        AND tmp.inscricao_economica = edf_tmp.inscricao_economica
)AS edf
ON
    acef.inscricao_economica = edf.inscricao_economica

LEFT JOIN (
    SELECT
        edi_tmp.timestamp,
        edi_tmp.inscricao_economica
    FROM
        economico.domicilio_informado AS edi_tmp,
        (
            SELECT
                MAX(timestamp) AS timestamp,
                inscricao_economica
            FROM
                economico.domicilio_informado
            GROUP BY
                inscricao_economica
        )AS tmp
    WHERE
        tmp.timestamp = edi_tmp.timestamp
        AND tmp.inscricao_economica = edi_tmp.inscricao_economica
)AS edi
ON
    acef.inscricao_economica = edi.inscricao_economica

LEFT JOIN
    arrecadacao.nota_servico AS ans
ON
    ans.timestamp = acef.timestamp
    AND ans.inscricao_economica = acef.inscricao_economica

LEFT JOIN
    arrecadacao.nota AS an
ON
    an.cod_nota = ans.cod_nota

LEFT JOIN
    arrecadacao.nota_fiscal AS anf
ON
    anf.cod_nota = ans.cod_nota

LEFT JOIN
    arrecadacao.nota_avulsa AS ana
ON
    ana.cod_nota = ans.cod_nota

LEFT JOIN
    arrecadacao.faturamento_servico AS afs
ON
    acef.inscricao_economica = afs.inscricao_economica
    AND acef.timestamp = afs.timestamp

LEFT JOIN
    arrecadacao.retencao_fonte AS arf
ON
    arf.inscricao_economica = acef.inscricao_economica
    AND arf.timestamp = acef.timestamp

LEFT JOIN
    arrecadacao.retencao_servico AS ars
ON
    ars.cod_retencao = arf.cod_retencao
    AND ars.inscricao_economica = acef.inscricao_economica
    AND ars.timestamp = acef.timestamp

LEFT JOIN
    arrecadacao.retencao_nota AS arn
ON
    arn.inscricao_economica = acef.inscricao_economica
    AND arn.timestamp = acef.timestamp
    AND arn.cod_retencao = ars.cod_retencao

INNER JOIN
    arrecadacao.cadastro_economico_calculo AS acec
ON
    acef.timestamp = acec.timestamp
    AND acef.inscricao_economica = acec.inscricao_economica

INNER JOIN
    arrecadacao.calculo AS ac
ON
    ac.cod_calculo = acec.cod_calculo
    AND ac.timestamp = acef.timestamp

LEFT JOIN
    arrecadacao.calculo_grupo_credito AS acgc
ON
    acgc.cod_calculo = ac.cod_calculo
    AND ac.exercicio = acgc.ano_exercicio

INNER JOIN
    arrecadacao.lancamento_calculo AS alc
ON
    ac.cod_calculo = alc.cod_calculo

INNER JOIN
    arrecadacao.lancamento AS al
ON
    alc.cod_lancamento = al.cod_lancamento

INNER JOIN
    arrecadacao.parcela AS ap
ON
    ap.cod_lancamento = al.cod_lancamento

LEFT JOIN
    (
        SELECT
            tmp.*
        FROM
            arrecadacao.parcela_reemissao AS tmp,
            (
                SELECT
                    timestamp,
                    cod_parcela
                FROM
                    arrecadacao.parcela_reemissao
                ORDER BY
                    timestamp DESC
            )AS tmp2
        WHERE
            tmp.cod_parcela = tmp2.cod_parcela
            and tmp.timestamp = tmp2.timestamp
    )AS apr
ON
    apr.cod_parcela = ap.cod_parcela

INNER JOIN
    arrecadacao.carne AS acne
ON
    acne.cod_parcela = ap.cod_parcela
WHERE
 ";

    return $stSql;
}

function recuperaConsultaNumeroNota(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = "";
    $stSql = $this->montaRecuperaConsultaNumeroNota().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaConsultaNumeroNota()
{
    $stSql = "  SELECT
                    an.nro_nota
                FROM
                    arrecadacao.nota_servico AS ans
                INNER JOIN
                    arrecadacao.nota AS an
                ON
                    an.cod_nota = ans.cod_nota
                WHERE ";

    return $stSql;
}

function recuperaArquivoNotaAvulsa(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaArquivoNotaAvulsa();
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaArquivoNotaAvulsa()
{
    $stSql = " SELECT
                    arquivos_documento.nome_arquivo_swx,
                    modelo_documento.nome_documento

                FROM
                    administracao.modelo_arquivos_documento

                INNER JOIN
                    administracao.arquivos_documento
                ON
                    arquivos_documento.cod_arquivo = modelo_arquivos_documento.cod_arquivo

                INNER JOIN
                    administracao.modelo_documento
                ON
                    modelo_documento.cod_documento = modelo_arquivos_documento.cod_documento

                WHERE
                    modelo_arquivos_documento.cod_acao = ".Sessao::read('acao');

    return $stSql;
}

function recuperaDadosNotaAvulsa(&$rsRecordSet, $stFiltro, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDadosNotaAvulsa().$stFiltro;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosNotaAvulsa()
{
    $stSql = "
        SELECT DISTINCT
            (
                SELECT
                    valor
                FROM
                    administracao.configuracao
                WHERE
                    cod_modulo = 2
                    AND exercicio = '".Sessao::getExercicio()."'
                    AND parametro = 'nom_prefeitura'
            ) AS prefeitura,
            (
                SELECT
                    valor
                FROM
                    administracao.configuracao
                WHERE
                    cod_modulo = 25
                    AND exercicio = '".Sessao::getExercicio()."'
                    AND parametro = 'carne_departamento'
            ) AS departamento,
            acec.inscricao_economica AS insc_eco_forn,
            to_char(afs.dt_emissao, 'dd/mm/yyyy' ) AS dt_emissao,
            afs.cod_atividade||' - '|| (
                SELECT
                    ea.nom_atividade
                FROM
                    economico.atividade AS ea
                WHERE
                    ea.cod_atividade = afs.cod_atividade
            )AS atividade_forn,
            (
                SELECT
                    cgm.nom_cgm
                FROM
                    sw_cgm AS cgm
                WHERE
                    COALESCE ( CEED.numcgm, CEEF.numcgm, CEA.numcgm ) = cgm.numcgm
            )AS nome_forn,
            (
                SELECT
                    cgm_pj.nom_fantasia
                FROM
                    sw_cgm_pessoa_juridica AS cgm_pj
                WHERE
                    cgm_pj.numcgm = COALESCE ( CEED.numcgm, CEEF.numcgm, CEA.numcgm )
            )AS razao_forn,
            (
                SELECT
                    COALESCE( sw_cgm_pessoa_juridica.cnpj, sw_cgm_pessoa_fisica.cpf )

                FROM
                    sw_cgm

                LEFT JOIN
                    sw_cgm_pessoa_juridica
                ON
                    sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm

                LEFT JOIN
                    sw_cgm_pessoa_fisica
                ON
                    sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm

                WHERE
                    sw_cgm.numcgm = COALESCE ( CEED.numcgm, CEEF.numcgm, CEA.numcgm )
            )AS cpf_cnpj_forn,

            CASE WHEN (edf.inscricao_municipal IS NOT NULL) AND (edi.inscricao_economica IS NOT NULL) THEN
                CASE WHEN (edf.timestamp > edi.timestamp) THEN
                    split_part ( economico.fn_busca_domicilio_fiscal( acef.inscricao_economica ), '§', 1)||' '||split_part ( economico.fn_busca_domicilio_fiscal( acef.inscricao_economica ), '§', 3)||', '||split_part ( economico.fn_busca_domicilio_fiscal( acef.inscricao_economica ), '§', 4)
                ELSE
                    split_part ( economico.fn_busca_domicilio_informado( acef.inscricao_economica ), '§', 1)||' '||split_part ( economico.fn_busca_domicilio_informado( acef.inscricao_economica ), '§', 3)||', '||split_part ( economico.fn_busca_domicilio_informado( acef.inscricao_economica ), '§', 4)
                END
            ELSE
                CASE WHEN (edf.inscricao_municipal IS NOT NULL) THEN
                    split_part ( economico.fn_busca_domicilio_fiscal( acef.inscricao_economica ), '§', 1)||' '||split_part ( economico.fn_busca_domicilio_fiscal( acef.inscricao_economica ), '§', 3)||', '||split_part ( economico.fn_busca_domicilio_fiscal( acef.inscricao_economica ), '§', 4)
                ELSE
                    split_part ( economico.fn_busca_domicilio_informado( acef.inscricao_economica ), '§', 1)||' '||split_part ( economico.fn_busca_domicilio_informado( acef.inscricao_economica ), '§', 3)||', '||split_part ( economico.fn_busca_domicilio_informado( acef.inscricao_economica ), '§', 4)
                END
            END AS endereco_forn,
            acef.competencia,
            afs.cod_servico,
            afs.cod_atividade,
            ana.nro_nota,
            ana.nro_serie AS serie,
            servico_sem_retencao.valor_declarado AS valor_serv_total,
            servico_sem_retencao.valor_declarado AS valor_serv,
            servico_sem_retencao.aliquota AS aliq_serv,
            1 AS qtd_serv,
            afs.cod_servico,
            (
                SELECT
                    cod_estrutural
                FROM
                    economico.servico
                WHERE
                    servico.cod_servico = afs.cod_servico
            )AS estrutura_serv,

            (
                SELECT
                    nom_servico
                FROM
                    economico.servico
                WHERE
                    servico.cod_servico = afs.cod_servico
            )AS desc_serv,

            (
                SELECT
                    COALESCE( sw_cgm_pessoa_juridica.cnpj, sw_cgm_pessoa_fisica.cpf )

                FROM
                    sw_cgm

                LEFT JOIN
                    sw_cgm_pessoa_juridica
                ON
                    sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm

                LEFT JOIN
                    sw_cgm_pessoa_fisica
                ON
                    sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm

                WHERE
                    sw_cgm.numcgm = ana.numcgm_tomador
            )AS cpf_cnpj_cli,
            arrecadacao.fn_consulta_endereco_todos( ana.numcgm_tomador, 3, 1 )||' '||arrecadacao.fn_consulta_endereco_todos( ana.numcgm_tomador, 3, 2 )||' '||arrecadacao.fn_consulta_endereco_todos( ana.numcgm_tomador, 3, 3 )||' '||arrecadacao.fn_consulta_endereco_todos( ana.numcgm_tomador, 3, 4 ) AS endereco_cli,
            (
                SELECT
                    cgm.nom_cgm
                FROM
                    sw_cgm AS cgm
                WHERE
                    ana.numcgm_tomador = cgm.numcgm
            )AS nome_cli,
            (
                SELECT
                    cgm_pj.nom_fantasia
                FROM
                    sw_cgm_pessoa_juridica AS cgm_pj
                WHERE
                    cgm_pj.numcgm = ana.numcgm_tomador
            )AS razao_cli,
            (
                SELECT
                    tomador_empresa.inscricao_economica

                FROM
                    arrecadacao.tomador_empresa

                WHERE
                    tomador_empresa.cod_nota = ana.cod_nota
            )AS insc_eco_cli,
            ana.observacao

        FROM
            economico.cadastro_economico AS ece

        INNER JOIN
            arrecadacao.cadastro_economico_faturamento AS acef
        ON
            acef.inscricao_economica = ece.inscricao_economica

        LEFT JOIN
            economico.cadastro_economico_empresa_direito CEED
        ON
            CEED.inscricao_economica = acef.inscricao_economica

        LEFT JOIN
            economico.cadastro_economico_empresa_fato CEEF
        ON
            CEEF.inscricao_economica = acef.inscricao_economica

        LEFT JOIN
            economico.cadastro_economico_autonomo CEA
        ON
            CEA.inscricao_economica = acef.inscricao_economica

        LEFT JOIN (
            SELECT
                edf_tmp.inscricao_economica,
                edf_tmp.inscricao_municipal,
                edf_tmp.timestamp
            FROM
                economico.domicilio_fiscal AS edf_tmp,
                (
                    SELECT
                        MAX (timestamp) AS timestamp,
                        inscricao_economica
                    FROM
                        economico.domicilio_fiscal
                    GROUP BY
                        inscricao_economica
                )AS tmp
            WHERE
                tmp.timestamp = edf_tmp.timestamp
                AND tmp.inscricao_economica = edf_tmp.inscricao_economica
        )AS edf
        ON
            acef.inscricao_economica = edf.inscricao_economica

        LEFT JOIN (
            SELECT
                edi_tmp.timestamp,
                edi_tmp.inscricao_economica
            FROM
                economico.domicilio_informado AS edi_tmp,
                (
                    SELECT
                        MAX(timestamp) AS timestamp,
                        inscricao_economica
                    FROM
                        economico.domicilio_informado
                    GROUP BY
                        inscricao_economica
                )AS tmp
            WHERE
                tmp.timestamp = edi_tmp.timestamp
                AND tmp.inscricao_economica = edi_tmp.inscricao_economica
        )AS edi
        ON
            acef.inscricao_economica = edi.inscricao_economica

        INNER JOIN
            arrecadacao.nota_servico AS ans
        ON
            ans.timestamp = acef.timestamp
            AND ans.inscricao_economica = acef.inscricao_economica

        INNER JOIN
            arrecadacao.nota AS an
        ON
            an.cod_nota = ans.cod_nota

        INNER JOIN
            arrecadacao.nota_avulsa AS ana
        ON
            ana.cod_nota = ans.cod_nota

        INNER JOIN
            arrecadacao.faturamento_servico AS afs
        ON
            acef.inscricao_economica = afs.inscricao_economica
            AND acef.timestamp = afs.timestamp

        INNER JOIN
            arrecadacao.servico_sem_retencao
        ON
            servico_sem_retencao.inscricao_economica = acef.inscricao_economica
            AND servico_sem_retencao.timestamp = acef.timestamp

        INNER JOIN
            arrecadacao.cadastro_economico_calculo AS acec
        ON
            acef.timestamp = acec.timestamp
            AND acef.inscricao_economica = acec.inscricao_economica

        INNER JOIN
            arrecadacao.calculo AS ac
        ON
            ac.cod_calculo = acec.cod_calculo
            AND ac.timestamp = acef.timestamp

        LEFT JOIN
            arrecadacao.calculo_grupo_credito AS acgc
        ON
            acgc.cod_calculo = ac.cod_calculo
            AND ac.exercicio = acgc.ano_exercicio

        INNER JOIN
            arrecadacao.lancamento_calculo AS alc
        ON
            ac.cod_calculo = alc.cod_calculo

        INNER JOIN
            arrecadacao.lancamento AS al
        ON
            alc.cod_lancamento = al.cod_lancamento

        LEFT JOIN
            arrecadacao.parcela AS ap
        ON
            ap.cod_lancamento = al.cod_lancamento

        LEFT JOIN
            (
                SELECT
                    tmp.*
                FROM
                    arrecadacao.parcela_reemissao AS tmp,
                    (
                        SELECT
                            timestamp,
                            cod_parcela
                        FROM
                            arrecadacao.parcela_reemissao
                        ORDER BY
                            timestamp DESC
                    )AS tmp2
                WHERE
                    tmp.cod_parcela = tmp2.cod_parcela
                    and tmp.timestamp = tmp2.timestamp
            )AS apr
        ON
            apr.cod_parcela = ap.cod_parcela

        LEFT JOIN
            arrecadacao.carne AS acne
        ON
            acne.cod_parcela = ap.cod_parcela

        WHERE ";

    return $stSql;
}
}
?>
