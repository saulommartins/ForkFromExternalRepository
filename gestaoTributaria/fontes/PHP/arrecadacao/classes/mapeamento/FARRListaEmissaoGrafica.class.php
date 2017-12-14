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
 * Classe de mapeamento da função para listar os dados para Emissão pela Gráfica
 * Data de Criação: 12/05/2005

 * @author Analista: Fabio Bertoldi Rodrigues
 * @author Desenvolvedor: Diego Bueno Coelho
 * @package URBEM
 * @subpackage Mapeamento

 * $Id: FARRListaEmissaoGrafica.class.php 63867 2015-10-27 17:25:14Z evandro $

 * Casos de uso: uc-05.03.11
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Data de Criação: 12/05/2005

  * @author Analista: Fabio Bertoldi Rodrigues
  * @author Desenvolvedor: Diego Bueno Coelho

  * @package URBEM
  * @subpackage Mapeamento
*/
class FARRListaEmissaoGrafica extends Persistente
{
    public $inExercicio;
    public $inCodGrupo;
    public $inCodCredito;
    public $inCodEspecie;
    public $inCodGenero;
    public $inCodNatureza;
    public $inCodIIInicial;
    public $inCodIIFinal;
    public $inCodIEInicial;
    public $inCodIEFinal;
    public $inCodEnderecoInicial;
    public $inCodEnderecoFinal;
    public $stLocalizacaoInicial;
    public $stLocalizacaoFinal;
    public $stTipoInscricao;
    public $stTipoEmissao;
    public $stOrdemEmissao;
    public $stOrdemEmissaoFuncao;

    # Atributos da Inscr. Imobiliaria
    public $stOrdemLote;
    public $stOrdemImovel;
    public $stOrdemEdificacao;

    # Atributos da Inscr. Economica
    public $stOrdemAtrFato;
    public $stOrdemAtrDireito;
    public $stOrdemAutonomo;
    public $stOrdemElemento;

/**
    * Método Construtor
    * @access Private
*/
function FARRListaEmissaoGrafica()
{
    parent::Persistente();
    $this->AddCampo('valor','varchar'  ,false       ,''     ,false   ,false );
    $this->inExercicio    		= '0';
    $this->inCodGrupo     		= '0';
    $this->inCodCredito   		= '0';
    $this->inCodEspecie   		= '0';
    $this->inCodGenero    		= '0';
    $this->inCodNatureza  		= '0';
    $this->inCodIIInicial 		= '0';
    $this->inCodIIFinal   		= '0';
    $this->stTipoInscricao		= null;

    $this->stLocalizacaoInicial = null;
    $this->stLocalizacaoFinal   = null;
    $this->inCodEnderecoInicial = '0';
    $this->inCodEnderecoFinal   = '0';

    $this->stVinculo			= null;
    $this->stOrdemEmissao		= null;
    $this->stOrdemEmissaoFuncao = '';
    
    $this->stOrdemLote			= null;
    $this->stOrdemImovel		= null;
    $this->stOrdemEdificacao	= null;

    $this->stOrdemAtrFato       = null;
    $this->stOrdemAtrDireito    = null;
    $this->stOrdemAutonomo      = null;
    $this->stOrdemElemento      = null;

    $this->stTipoEmissao        = null;
    $this->stPadraoCodBarra     = null;

}

function listaEmpresas(&$rsRecordset,$boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $stFiltroCredito = "";
    $stFiltroGeral = "";

    if ( ($this->inCodCredito != '0') && ($this->inCodEspecie != '0') && ($this->inCodGenero != '0') && ($this->inCodNatureza != '0') ) {
        $stFiltroCredito = "
            WHERE  acgc.cod_calculo IS NULL
              AND  ac.cod_credito  = ".$this->inCodCredito."
              AND  ac.cod_especie  = ".$this->inCodEspecie."
              AND  ac.cod_genero   = ".$this->inCodGenero."
              AND  ac.cod_natureza = ".$this->inCodNatureza."
        ";

        if ($this->inExercicio != '0') {
            $stFiltroCredito .= " AND ac.exercicio = ".$this->inExercicio;
        }
    } else {
        if ($this->inCodGrupo != '0') {
            $stFiltroCredito = "
                WHERE  acgc.cod_grupo = ".$this->inCodGrupo;

            if ($this->inExercicio != '0') {
                $stFiltroCredito .= " AND  acgc.ano_exercicio = '".$this->inExercicio."'";
            }
        }
    }

    if ($this->inCodIEInicial > 0) {
        if ($this->inCodIEFinal > 0) {
            $stFiltroGeral .= " AND cec.inscricao_economica BETWEEN ".$this->inCodIEInicial." AND ".$this->inCodIEFinal;
        } else {
            $stFiltroGeral .= " AND cec.inscricao_economica = ".$this->inCodIEInicial;
        }
    }

    if ($this->inCodEnderecoInicial > 0) {
        if ($this->inCodEnderecoFinal > 0) {
            $stFiltroGeral .= " and ENDERECO.cod_lote BETWEEN ".$this->inCodEnderecoInicial." and ".$this->inCodEnderecoFinal;
        } else {
            $stFiltroGeral .= " and ENDERECO.cod_lote = ".$this->inCodEnderecoInicial;
        }
    }

    if ($this->stLocalizacaoInicial != "") {
        if ($this->stLocalizacaoFinal != "") {
            $stFiltroGeral .= " AND  ILOC.codigo_composto BETWEEN '".$this->stLocalizacaoInicial."' AND '".$this->stLocalizacaoFinal."'";
        } else {
            $stFiltroGeral .= " AND  ILOC.codigo_composto = '".$this->stLocalizacaoInicial."'";
        }
    }

    $stSql  = $this->montaListaEmpresas( $stFiltroCredito, $stFiltroGeral );
    $this->setDebug($stSql);

    $obErro = $obConexao->executaSQL($rsRecordset,$stSql, $boTransacao );

    return $obErro;
}

function montaListaEmpresas($stFiltroCredito, $stFiltroGeral)
{
    $stSql = "
        SELECT DISTINCT cec.inscricao_economica AS inscricao

          FROM arrecadacao.cadastro_economico_calculo as cec

    INNER JOIN (
                    SELECT  cec.inscricao_economica as inscricao
                         ,  max(ac.cod_calculo) as cod_calculo
                      FROM  arrecadacao.cadastro_economico_calculo as cec

                 LEFT JOIN  arrecadacao.calculo_grupo_credito as acgc
                        ON  acgc.cod_calculo = cec.cod_calculo

                 LEFT JOIN  arrecadacao.grupo_credito as agc
                        ON  agc.cod_grupo = acgc.cod_grupo
                       AND  agc.ano_exercicio = acgc.ano_exercicio

                INNER JOIN  arrecadacao.calculo as ac
                        ON  ac.cod_calculo = cec.cod_calculo

                    ".$stFiltroCredito."

                  GROUP BY  cec.inscricao_economica, ac.exercicio
               ) AS aic2

            ON  aic2.inscricao = cec.inscricao_economica
           AND  aic2.cod_calculo = cec.cod_calculo

    INNER JOIN  arrecadacao.lancamento_calculo as alc
            ON  alc.cod_calculo = cec.cod_calculo

    INNER JOIN  arrecadacao.lancamento as al
            ON  al.cod_lancamento = alc.cod_lancamento

    INNER JOIN  (
                SELECT  ccgm.numcgm, ccgm.cod_calculo, cgm.nom_cgm

                  FROM  arrecadacao.calculo_cgm as ccgm

            INNER JOIN  sw_cgm as cgm
                    ON  cgm.numcgm = ccgm.numcgm
                ) as cgm
            ON  cgm.cod_calculo = cec.cod_calculo

    INNER JOIN  sw_cgm_pessoa_juridica as pjcgm
            ON  cgm.numcgm = cgm.numcgm

     LEFT JOIN  economico.domicilio_fiscal as edef
             ON  edef.inscricao_economica = cec.inscricao_economica

     LEFT JOIN  economico.domicilio_informado as edei
            ON  edei.inscricao_economica = cec.inscricao_economica

     LEFT JOIN  economico.cadastro_economico_empresa_direito CEED
            ON  CEED.inscricao_economica = cec.inscricao_economica

     LEFT JOIN  economico.cadastro_economico_empresa_fato CEEF
            ON  CEEF.inscricao_economica = cec.inscricao_economica

     LEFT JOIN  economico.cadastro_economico_autonomo CEA
            ON  CEA.inscricao_economica = cec.inscricao_economica

         WHERE  al.cod_lancamento IS NOT NULL
           AND  al.valor > 0.00
           AND  COALESCE (CEED.numcgm, CEEF.numcgm, CEA.numcgm) = cgm.numcgm
           AND  pjcgm.numcgm = cgm.numcgm

    ".$stFiltroGeral;

    return $stSql;
}

function listaImoveis(&$rsRecordset,$boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stFiltroCredito = "";
    $stFiltroGeral = "";

    if ( ($this->inCodCredito != '0') && ($this->inCodEspecie != '0') && ($this->inCodGenero != '0') && ($this->inCodNatureza != '0') ) {
        $stFiltroCredito = "
            WHERE
                acgc.cod_calculo IS NULL
                and ac.cod_credito      = ".$this->inCodCredito."
                and ac.cod_especie      = ".$this->inCodEspecie."
                and ac.cod_genero       = ".$this->inCodGenero."
                and ac.cod_natureza     = ".$this->inCodNatureza."
        ";

        if ($this->inExercicio != '0') {
            $stFiltroCredito .= " and ac.exercicio = '".$this->inExercicio."'";
        }
    }else
        if ($this->inCodGrupo != '0') {
            $stFiltroCredito = "
                WHERE
                    acgc.cod_grupo = ".$this->inCodGrupo;

            if ($this->inExercicio != '0') {
                $stFiltroCredito .= " and acgc.ano_exercicio = '".$this->inExercicio."'";
            }
        }

    if ($this->stTipoInscricao == "prediais") {
        $stFiltroGeral .= "
            and exists (
                select
                    inscricao_municipal
                from
                    imobiliario.unidade_autonoma as iau
                where
                    iau.inscricao_municipal = aic2.inscricao
            )
        ";
    }else
        if ($this->stTipoInscricao == "territoriais") {
            $stFiltroGeral .= "
                and not exists (
                    select
                        inscricao_municipal
                    from
                        imobiliario.unidade_autonoma as iau
                    where
                        iau.inscricao_municipal = aic2.inscricao
                )
            ";
        }

    if ($this->inCodIIInicial >= 0) {
        if ($this->inCodIIFinal > 0) {
            $stFiltroGeral .= " and aic2.inscricao between ".$this->inCodIIInicial." and ".$this->inCodIIFinal;
        } else {
            $stFiltroGeral .= " and aic.inscricao_municipal = ".$this->inCodIIInicial;
        }
    }

    if ($this->inCodEnderecoInicial > 0) {
        if ($this->inCodEnderecoFinal > 0) {
            $stFiltroGeral .= " and ENDERECO.cod_lote between ".$this->inCodEnderecoInicial." and ".$this->inCodEnderecoFinal;
        } else {
            $stFiltroGeral .= " and ENDERECO.cod_lote = ".$this->inCodEnderecoInicial;
        }
    }

    if ($this->stLocalizacaoInicial != "") {
        if ($this->stLocalizacaoFinal != "") {
            $stFiltroGeral .= " and ILOC.codigo_composto between '".$this->stLocalizacaoInicial."' and '".$this->stLocalizacaoFinal."'";
        } else {
            $stFiltroGeral .= " and ILOC.codigo_composto = '".$this->stLocalizacaoInicial."'";
        }
    }
    
    $stSql  = $this->montaListaImoveis( $stFiltroCredito, $stFiltroGeral );
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL($rsRecordset,$stSql, $boTransacao );
    return $obErro;
}

function montaListaImoveis($stFiltroCredito, $stFiltroGeral)
{
    $stSql = "
        SELECT DISTINCT
            aic.inscricao_municipal AS inscricao

        FROM
            arrecadacao.imovel_calculo as aic

        INNER JOIN (
            select
                aic.inscricao_municipal as inscricao
                , max(ac.cod_calculo) as cod_calculo

            from
                arrecadacao.imovel_calculo as aic

            LEFT JOIN
                arrecadacao.calculo_grupo_credito as acgc
            ON
                acgc.cod_calculo = aic.cod_calculo

            LEFT JOIN
                arrecadacao.grupo_credito as agc
            ON
                agc.cod_grupo = acgc.cod_grupo
                and agc.ano_exercicio = acgc.ano_exercicio

            INNER JOIN
                arrecadacao.calculo as ac
            ON
                ac.cod_calculo = aic.cod_calculo


            INNER JOIN arrecadacao.lancamento_calculo AS lc
            ON lc.cod_calculo = ac.cod_calculo

            INNER JOIN arrecadacao.lancamento AS l
            ON l.cod_lancamento = lc.cod_lancamento
            and l.ativo = true

            ".$stFiltroCredito."

            group by
                aic.inscricao_municipal, ac.exercicio

        ) as aic2
        ON
            aic2.inscricao = aic.inscricao_municipal
            AND aic2.cod_calculo = aic.cod_calculo

        INNER JOIN
            arrecadacao.lancamento_calculo as alc
        ON
            alc.cod_calculo = aic.cod_calculo

        INNER JOIN
            arrecadacao.lancamento as al
        ON
            al.cod_lancamento = alc.cod_lancamento
            AND al.ativo = true

        INNER JOIN (
            SELECT
                ccgm.numcgm, ccgm.cod_calculo, cgm.nom_cgm
            FROM
                arrecadacao.calculo_cgm as ccgm
            INNER JOIN
                sw_cgm as cgm
            ON
                cgm.numcgm = ccgm.numcgm
        ) as cgm
        ON
            cgm.cod_calculo = aic.cod_calculo

        INNER JOIN (
            SELECT
                IML.inscricao_municipal
                , IML.cod_lote
                , max(IML.timestamp) as timestamp
            FROM
                imobiliario.imovel_lote as IML
            GROUP BY
                IML.inscricao_municipal, IML.cod_lote
        ) as IML
        ON
            IML.inscricao_municipal = aic.inscricao_municipal

        INNER JOIN
            imobiliario.lote_localizacao as ILLO
        ON
            ILLO.cod_lote = IML.cod_lote

        INNER JOIN
            imobiliario.localizacao as ILOC
        ON
            ILOC.cod_localizacao = ILLO.cod_localizacao

        INNER JOIN (
            select
                ial.cod_lote, ial.area_real
            from
                imobiliario.area_lote as ial
            INNER JOIN (
                select
                    cod_lote, max(timestamp) as timestamp
                from
                    imobiliario.area_lote as ial2
                group by
                    cod_lote
            ) as ial2 ON ial2.cod_lote = ial.cod_lote and ial2.timestamp = ial.timestamp
        ) as ial
        ON
            ial.cod_lote = IML.cod_lote

        INNER JOIN
            arrecadacao.imovel_v_venal as vvenal
        ON
            vvenal.inscricao_municipal = aic.inscricao_municipal
            AND vvenal.timestamp = aic.timestamp

        LEFT JOIN
            imobiliario.baixa_imovel as ibi
        ON
            ibi.inscricao_municipal = aic.inscricao_municipal

        LEFT JOIN (
            select
                iua.inscricao_municipal, cod_construcao, cod_tipo
            from
                imobiliario.unidade_autonoma as iua
                inner join (
                    select
                        coalesce (inscricao_municipal, null) as inscricao_municipal
                        , max(timestamp) as timestamp
                    from imobiliario.unidade_autonoma as iua2
                    group by inscricao_municipal
                ) as iua2
                ON iua2.inscricao_municipal = iua.inscricao_municipal
                and iua2.timestamp = iua.timestamp
        ) as iua
        ON iua.inscricao_municipal = aic.inscricao_municipal

        WHERE
            al.cod_lancamento is not null
            and ibi.inscricao_municipal is null
            and al.valor > 0.00
    ".$stFiltroGeral;

    return $stSql;
}
function listaImoveisDesonerados(&$rsRecordset,$boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $stFiltroCredito = "";
    $stFiltroGeral = "";

    if ( ($this->inCodCredito != '0') && ($this->inCodEspecie != '0') && ($this->inCodGenero != '0') && ($this->inCodNatureza != '0') ) {
        $stFiltroCredito = "
            WHERE
                acgc.cod_calculo IS NULL
                and ac.cod_credito      = ".$this->inCodCredito."
                and ac.cod_especie      = ".$this->inCodEspecie."
                and ac.cod_genero       = ".$this->inCodGenero."
                and ac.cod_natureza     = ".$this->inCodNatureza."
        ";

        if ($this->inExercicio != '0') {
            $stFiltroCredito .= " and ac.exercicio = ".$this->inExercicio;
        }
    }else
        if ($this->inCodGrupo != '0') {
            $stFiltroCredito = "
                WHERE
                    acgc.cod_grupo = ".$this->inCodGrupo;

            if ($this->inExercicio != '0') {
                $stFiltroCredito .= " and acgc.ano_exercicio = '".$this->inExercicio."'";
            }
        }

    if ($this->stTipoInscricao == "prediais") {
        $stFiltroGeral .= "
            and exists (
                select
                    inscricao_municipal
                from
                    imobiliario.unidade_autonoma as iau
                where
                    iau.inscricao_municipal = aic2.inscricao
            )
        ";
    }else
        if ($this->stTipoInscricao == "territoriais") {
            $stFiltroGeral .= "
                and not exists (
                    select
                        inscricao_municipal
                    from
                        imobiliario.unidade_autonoma as iau
                    where
                        iau.inscricao_municipal = aic2.inscricao
                )
            ";
        }

    if ($this->inCodIIInicial >= 0) {
        if ($this->inCodIIFinal > 0) {
            $stFiltroGeral .= " and aic2.inscricao between ".$this->inCodIIInicial." and ".$this->inCodIIFinal;
        } else {
            $stFiltroGeral .= " and aic.inscricao_municipal = ".$this->inCodIIInicial;
        }
    }

    if ($this->inCodEnderecoInicial > 0) {
        if ($this->inCodEnderecoFinal > 0) {
            $stFiltroGeral .= " and ENDERECO.cod_lote between ".$this->inCodEnderecoInicial." and ".$this->inCodEnderecoFinal;
        } else {
            $stFiltroGeral .= " and ENDERECO.cod_lote = ".$this->inCodEnderecoInicial;
        }
    }

    if ($this->stLocalizacaoInicial != "") {
        if ($this->stLocalizacaoFinal != "") {
            $stFiltroGeral .= " and ILOC.codigo_composto between '".$this->stLocalizacaoInicial."' and '".$this->stLocalizacaoFinal."'";
        } else {
            $stFiltroGeral .= " and ILOC.codigo_composto = '".$this->stLocalizacaoInicial."'";
        }
    }

    $stSql  = $this->montaListaImoveisDesonerados( $stFiltroCredito, $stFiltroGeral );
    $this->setDebug($stSql);
    
    $obErro = $obConexao->executaSQL($rsRecordset,$stSql, $boTransacao );

    return $obErro;
}

function montaListaImoveisDesonerados($stFiltroCredito, $stFiltroGeral)
{
    $stSql = "
        SELECT DISTINCT aic.inscricao_municipal AS inscricao
          FROM arrecadacao.imovel_calculo as aic
    INNER JOIN (
                    SELECT aic.inscricao_municipal as inscricao
                         , max(ac.cod_calculo) as cod_calculo
                      FROM arrecadacao.imovel_calculo as aic

                 LEFT JOIN arrecadacao.calculo_grupo_credito as acgc
                        ON acgc.cod_calculo = aic.cod_calculo

                 LEFT JOIN arrecadacao.grupo_credito as agc
                        ON agc.cod_grupo = acgc.cod_grupo
                       AND agc.ano_exercicio = acgc.ano_exercicio

                INNER JOIN arrecadacao.calculo as ac
                        ON ac.cod_calculo = aic.cod_calculo

                INNER JOIN arrecadacao.lancamento_calculo AS lc
                        ON lc.cod_calculo = ac.cod_calculo

                INNER JOIN arrecadacao.lancamento AS l
                        ON l.cod_lancamento = lc.cod_lancamento

                INNER JOIN arrecadacao.lancamento_usa_desoneracao
                        ON lancamento_usa_desoneracao.cod_lancamento = lc.cod_lancamento
                       AND lancamento_usa_desoneracao.cod_calculo = lc.cod_calculo

                    ".$stFiltroCredito."

                  GROUP BY aic.inscricao_municipal, ac.exercicio

               ) as aic2
            ON aic2.inscricao = aic.inscricao_municipal
           AND aic2.cod_calculo = aic.cod_calculo

    INNER JOIN arrecadacao.lancamento_calculo as alc
            ON alc.cod_calculo = aic.cod_calculo

    INNER JOIN arrecadacao.lancamento as al
            ON al.cod_lancamento = alc.cod_lancamento


    INNER JOIN (
                    SELECT ccgm.numcgm, ccgm.cod_calculo, cgm.nom_cgm
                      FROM arrecadacao.calculo_cgm as ccgm
                INNER JOIN sw_cgm as cgm
                        ON cgm.numcgm = ccgm.numcgm
                ) as cgm
            ON cgm.cod_calculo = aic.cod_calculo

    INNER JOIN (
                    SELECT IML.inscricao_municipal
                         , IML.cod_lote
                         , max(IML.timestamp) as timestamp
                    FROM imobiliario.imovel_lote as IML
                GROUP BY IML.inscricao_municipal, IML.cod_lote
                ) as IML
            ON IML.inscricao_municipal = aic.inscricao_municipal

    INNER JOIN imobiliario.lote_localizacao as ILLO
            ON ILLO.cod_lote = IML.cod_lote

    INNER JOIN imobiliario.localizacao as ILOC
            ON ILOC.cod_localizacao = ILLO.cod_localizacao

    INNER JOIN (
                    SELECT ial.cod_lote, ial.area_real
                      FROM imobiliario.area_lote as ial
                INNER JOIN (
                                SELECT cod_lote, max(timestamp) as timestamp
                                  FROM imobiliario.area_lote as ial2
                              GROUP BY cod_lote
                            ) as ial2 ON ial2.cod_lote = ial.cod_lote and ial2.timestamp = ial.timestamp
                ) as ial
            ON ial.cod_lote = IML.cod_lote

    INNER JOIN arrecadacao.imovel_v_venal as vvenal
            ON vvenal.inscricao_municipal = aic.inscricao_municipal
            AND vvenal.timestamp = aic.timestamp

     LEFT JOIN imobiliario.baixa_imovel as ibi
            ON ibi.inscricao_municipal = aic.inscricao_municipal

     LEFT JOIN (
                    SELECT iua.inscricao_municipal, cod_construcao, cod_tipo
                      FROM imobiliario.unidade_autonoma as iua
                INNER JOIN (
                                SELECT coalesce (inscricao_municipal, null) as inscricao_municipal
                                     , max(timestamp) as timestamp
                                  FROM imobiliario.unidade_autonoma as iua2
                              GROUP BY inscricao_municipal
                            ) as iua2
                        ON iua2.inscricao_municipal = iua.inscricao_municipal
                       AND iua2.timestamp = iua.timestamp
                ) as iua
            ON iua.inscricao_municipal = aic.inscricao_municipal

        WHERE al.cod_lancamento is not null
     --     AND ibi.inscricao_municipal is null

         -- AND al.valor = 0.00
    ".$stFiltroGeral;

    return $stSql;
}

function executaFuncao(&$rsRecordset, $stParametros,$boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;

    if ($this->stTipoEmissao == "II") { #|| !$this->stTipoEmissao ) {
        $stSql  = $this->montaExecutaFuncaoImobiliaria();
    } elseif ($this->stTipoEmissao == "IE" || !$this->stTipoEmissao) {
        $stSql  = $this->montaExecutaFuncaoEconomica();
    }
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL($rsRecordset,$stSql, $boTransacao );

    return $obErro;
}

function montaExecutaFuncaoImobiliaria()
{
    $stSql  = " SELECT                                                                  \r\n";
    $stSql .= "     *                                                                   \r\n";
    $stSql .= " FROM arrecadacao.fn_lista_emissao_grafica_imobiliaria (
                '".$this->stTipoInscricao."',
                ".$this->inExercicio.",
                ".$this->inCodGrupo.",
                ".$this->inCodCredito.",
                ".$this->inCodEspecie.",
                ".$this->inCodGenero.",
                ".$this->inCodNatureza.",
                '".$this->inCodIIInicial."',
                ".$this->inCodIIFinal.",
                '".$this->stLocalizacaoInicial."',
                '".$this->stLocalizacaoFinal."',
                ".$this->inCodEnderecoInicial.",
                ".$this->inCodEnderecoFinal.",
                '".$this->stOrdemEmissao."',
                '".$this->stOrdemLote."',
                '".$this->stOrdemImovel."',
                '".$this->stOrdemEdificacao."',
                '".$this->stPadraoCodBarra."'
            )                                                                       \r\n";
    $stSql .= " as lista_emissao (                                                  \r\n";
    $stSql .= " inscricao int,
                exercicio int,
                cod_lancamento  int,
                lancamento_vencimento date,
                lancamento_valor numeric,
                cod_grupo int,
                nom_grupo varchar,
                numcgm int,
                nom_cgm varchar,
        
                area_lote numeric,
                area_construida numeric,
                codigo_composto varchar,
                nom_localizacao varchar,
                cod_lote int,
                cod_construcao int,
                cod_tipo_construcao int,
        
                nom_tipo_logradouro varchar,
                cod_logradouro int,
                nom_logradouro varchar,
                numero varchar,
                complemento varchar,
                nome_condominio varchar,
                nom_bairro varchar,
                cep varchar,
                cod_municipio varchar,
                nom_municipio varchar,
                cod_uf varchar,
                sigla_uf varchar,
        
                c_nom_tipo_logradouro varchar ,
                c_cod_logradouro int,
                c_nom_logradouro varchar,
                c_numero varchar,
                c_complemento varchar,
                c_nom_bairro varchar,
                c_cep varchar,
                c_cod_municipio varchar,
                c_nom_municipio varchar,
                c_cod_uf varchar,
                c_sigla_uf varchar,
                c_caixa_postal varchar
        
                , qtde_parcelas_unicas varchar
        
                , cod_parcela_unica_1 varchar
                , valor_unica_1 varchar
                , vencimento_unica_1 varchar
                , desconto_unica_1 varchar
                , numeracao_unica_1 varchar
                , nosso_numero_unica_1 varchar
                , codigo_barra_unica_1 varchar
                , linha_digitavel_unica_1 varchar
        
                , cod_parcela_unica_2 varchar
                , valor_unica_2 varchar
                , vencimento_unica_2 varchar
                , desconto_unica_2 varchar
                , numeracao_unica_2 varchar
                , nosso_numero_unica_2 varchar
                , codigo_barra_unica_2 varchar
                , linha_digitavel_unica_2 varchar
        
                , cod_parcela_unica_3 varchar
                , valor_unica_3 varchar
                , vencimento_unica_3 varchar
                , desconto_unica_3 varchar
                , numeracao_unica_3 varchar
                , nosso_numero_unica_3 varchar
                , codigo_barra_unica_3 varchar
                , linha_digitavel_unica_3 varchar
        
                , cod_parcela_unica_4 varchar
                , valor_unica_4 varchar
                , vencimento_unica_4 varchar
                , desconto_unica_4 varchar
                , numeracao_unica_4 varchar
                , nosso_numero_unica_4 varchar
                , codigo_barra_unica_4 varchar
                , linha_digitavel_unica_4 varchar
        
                , cod_parcela_unica_5 varchar
                , valor_unica_5 varchar
                , vencimento_unica_5 varchar
                , desconto_unica_5 varchar
                , numeracao_unica_5 varchar
                , nosso_numero_unica_5 varchar
                , codigo_barra_unica_5 varchar
                , linha_digitavel_unica_5 varchar

                , qtde_parcelas_normais varchar
        
                , cod_parcela_normal_1 varchar
                , valor_normal_1 varchar
                , vencimento_normal_1 varchar
                , numeracao_normal_1 varchar
                , nosso_numero_normal_1 varchar
                , codigo_barra_normal_1 varchar
                , linha_digitavel_normal_1 varchar
        
                , cod_parcela_normal_2 varchar
                , valor_normal_2 varchar
                , vencimento_normal_2 varchar
                , numeracao_normal_2 varchar
                , nosso_numero_normal_2 varchar
                , codigo_barra_normal_2 varchar
                , linha_digitavel_normal_2 varchar
        
                , cod_parcela_normal_3 varchar
                , valor_normal_3 varchar
                , vencimento_normal_3 varchar
                , numeracao_normal_3 varchar
                , nosso_numero_normal_3 varchar
                , codigo_barra_normal_3 varchar
                , linha_digitavel_normal_3 varchar
        
                , cod_parcela_normal_4 varchar
                , valor_normal_4 varchar
                , vencimento_normal_4 varchar
                , numeracao_normal_4 varchar
                , nosso_numero_normal_4 varchar
                , codigo_barra_normal_4 varchar
                , linha_digitavel_normal_4 varchar
        
                , cod_parcela_normal_5 varchar
                , valor_normal_5 varchar
                , vencimento_normal_5 varchar
                , numeracao_normal_5 varchar
                , nosso_numero_normal_5 varchar
                , codigo_barra_normal_5 varchar
                , linha_digitavel_normal_5 varchar
        
                , cod_parcela_normal_6 varchar
                , valor_normal_6 varchar
                , vencimento_normal_6 varchar
                , numeracao_normal_6 varchar
                , nosso_numero_normal_6 varchar
                , codigo_barra_normal_6 varchar
                , linha_digitavel_normal_6 varchar
        
                , cod_parcela_normal_7 varchar
                , valor_normal_7 varchar
                , vencimento_normal_7 varchar
                , numeracao_normal_7 varchar
                , nosso_numero_normal_7 varchar
                , codigo_barra_normal_7 varchar
                , linha_digitavel_normal_7 varchar
        
                , cod_parcela_normal_8 varchar
                , valor_normal_8 varchar
                , vencimento_normal_8 varchar
                , numeracao_normal_8 varchar
                , nosso_numero_normal_8 varchar
                , codigo_barra_normal_8 varchar
                , linha_digitavel_normal_8 varchar
        
                , cod_parcela_normal_9 varchar
                , valor_normal_9 varchar
                , vencimento_normal_9 varchar
                , numeracao_normal_9 varchar
                , nosso_numero_normal_9 varchar
                , codigo_barra_normal_9 varchar
                , linha_digitavel_normal_9 varchar
        
                , cod_parcela_normal_10 varchar
                , valor_normal_10 varchar
                , vencimento_normal_10 varchar
                , numeracao_normal_10 varchar
                , nosso_numero_normal_10 varchar
                , codigo_barra_normal_10 varchar
                , linha_digitavel_normal_10 varchar
        
                , cod_parcela_normal_11 varchar
                , valor_normal_11 varchar
                , vencimento_normal_11 varchar
                , numeracao_normal_11 varchar
                , nosso_numero_normal_11 varchar
                , codigo_barra_normal_11 varchar
                , linha_digitavel_normal_11 varchar
        
                , cod_parcela_normal_12 varchar
                , valor_normal_12 varchar
                , vencimento_normal_12 varchar
                , numeracao_normal_12 varchar
                , nosso_numero_normal_12 varchar
                , codigo_barra_normal_12 varchar
                , linha_digitavel_normal_12 varchar
        
                , soma_creditos varchar
        
                , cod_credito_1 varchar
                , descricao_1 varchar
                , valor_1 varchar
        
                , cod_credito_2 varchar
                , descricao_2 varchar
                , valor_2 varchar
        
                , cod_credito_3 varchar
                , descricao_3 varchar
                , valor_3 varchar
        
                , cod_credito_4 varchar
                , descricao_4 varchar
                , valor_4 varchar
        
                , cod_credito_5 varchar
                , descricao_5 varchar
                , valor_5 varchar
        
                , cod_credito_6 varchar
                , descricao_6 varchar
                , valor_6 varchar
        
                , cod_credito_7 varchar
                , descricao_7 varchar
                , valor_7 varchar
        
                , valor_venal_territorial varchar
                , valor_venal_predial varchar
                , valor_venal_total varchar
        
                , valor_m2_territorial varchar
                , valor_m2_predial varchar
        
                , localizacao_primeiro_nivel varchar
                , valor_imposto varchar
        
                , area_limpeza varchar
                , aliquota_limpeza varchar
        
                , aliquota_imposto varchar
        
                , atributo_1 varchar
                , atributo_2 varchar
                , atributo_3 varchar
                , atributo_4 varchar
                , atributo_5 varchar
                , atributo_6 varchar
                , atributo_7 varchar
                , atributo_8 varchar
                , atributo_9 varchar
                , atributo_10 varchar
                , atributo_11 varchar
                , atributo_12 varchar
                , atributo_13 varchar
                , atributo_14 varchar
                , atributo_15 varchar
                , valor_m2_predial_descoberto varchar
                , valor_venal_predial_descoberto varchar
                , area_construida_total varchar
                , area_descoberta varchar
                , valor_venal_predial_coberto varchar
                                                               \r\n";
    $stSql .= " ) ORDER BY ".$this->stOrdemEmissaoFuncao." \r\n";

    return $stSql;

}

function executaFuncaoDesonerados(&$rsRecordset, $stParametros,$boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;

    if ($this->stTipoEmissao == "II") {
        $stSql  = $this->montaExecutaFuncaoImobiliariaDesonerados();
    } elseif ($this->stTipoEmissao == "IE" || !$this->stTipoEmissao) {
        $stSql  = $this->montaExecutaFuncaoEconomica();
    }
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL($rsRecordset,$stSql, $boTransacao );

    return $obErro;
}

function montaExecutaFuncaoImobiliariaDesonerados()
{
    $stSql  = " SELECT                                                                  \r\n";
    $stSql .= "     *                                                                   \r\n";
    $stSql .= " FROM arrecadacao.fn_lista_emissao_grafica_imobiliaria_emissao_isento (
                '".$this->stTipoInscricao."',
                ".$this->inExercicio.",
                ".$this->inCodGrupo.",
                ".$this->inCodCredito.",
                ".$this->inCodEspecie.",
                ".$this->inCodGenero.",
                ".$this->inCodNatureza.",
                '".$this->inCodIIInicial."',
                ".$this->inCodIIFinal.",
                '".$this->stLocalizacaoInicial."',
                '".$this->stLocalizacaoFinal."',
                ".$this->inCodEnderecoInicial.",
                ".$this->inCodEnderecoFinal.",
                '".$this->stOrdemEmissao."',
                '".$this->stOrdemLote."',
                '".$this->stOrdemImovel."',
                '".$this->stOrdemEdificacao."',
                '".$this->stPadraoCodBarra."'
            )                                                                       \r\n";
    $stSql .= " as lista_emissao (                                                  \r\n";
    $stSql .= " inscricao int,
                exercicio int,
                cod_lancamento  int,
                lancamento_vencimento date,
                lancamento_valor numeric,
                cod_grupo int,
                nom_grupo varchar,
                numcgm int,
                nom_cgm varchar,

                area_lote numeric,
                area_construida numeric,
                codigo_composto varchar,
                nom_localizacao varchar,
                cod_lote int,
                cod_construcao int,
                cod_tipo_construcao int,
        
                nom_tipo_logradouro varchar,
                cod_logradouro int,
                nom_logradouro varchar,
                numero varchar,
                complemento varchar,
                nom_bairro varchar,
                cep varchar,
                cod_municipio varchar,
                nom_municipio varchar,
                cod_uf varchar,
                sigla_uf varchar,
        
                c_nom_tipo_logradouro varchar ,
                c_cod_logradouro int,
                c_nom_logradouro varchar,
                c_numero varchar,
                c_complemento varchar,
                c_nom_bairro varchar,
                c_cep varchar,
                c_cod_municipio varchar,
                c_nom_municipio varchar,
                c_cod_uf varchar,
                c_sigla_uf varchar,
                c_caixa_postal varchar
        
                , qtde_parcelas_unicas varchar
        
                , cod_parcela_unica_1 varchar
                , valor_unica_1 varchar
                , vencimento_unica_1 varchar
                , desconto_unica_1 varchar
                , numeracao_unica_1 varchar
                , nosso_numero_unica_1 varchar
                , codigo_barra_unica_1 varchar
                , linha_digitavel_unica_1 varchar
        
                , cod_parcela_unica_2 varchar
                , valor_unica_2 varchar
                , vencimento_unica_2 varchar
                , desconto_unica_2 varchar
                , numeracao_unica_2 varchar
                , nosso_numero_unica_2 varchar
                , codigo_barra_unica_2 varchar
                , linha_digitavel_unica_2 varchar
        
                , cod_parcela_unica_3 varchar
                , valor_unica_3 varchar
                , vencimento_unica_3 varchar
                , desconto_unica_3 varchar
                , numeracao_unica_3 varchar
                , nosso_numero_unica_3 varchar
                , codigo_barra_unica_3 varchar
                , linha_digitavel_unica_3 varchar
        
                , cod_parcela_unica_4 varchar
                , valor_unica_4 varchar
                , vencimento_unica_4 varchar
                , desconto_unica_4 varchar
                , numeracao_unica_4 varchar
                , nosso_numero_unica_4 varchar
                , codigo_barra_unica_4 varchar
                , linha_digitavel_unica_4 varchar
        
                , cod_parcela_unica_5 varchar
                , valor_unica_5 varchar
                , vencimento_unica_5 varchar
                , desconto_unica_5 varchar
                , numeracao_unica_5 varchar
                , nosso_numero_unica_5 varchar
                , codigo_barra_unica_5 varchar
                , linha_digitavel_unica_5 varchar
        
        
                , qtde_parcelas_normais varchar
        
                , cod_parcela_normal_1 varchar
                , valor_normal_1 varchar
                , vencimento_normal_1 varchar
                , numeracao_normal_1 varchar
                , nosso_numero_normal_1 varchar
                , codigo_barra_normal_1 varchar
                , linha_digitavel_normal_1 varchar
        
                , cod_parcela_normal_2 varchar
                , valor_normal_2 varchar
                , vencimento_normal_2 varchar
                , numeracao_normal_2 varchar
                , nosso_numero_normal_2 varchar
                , codigo_barra_normal_2 varchar
                , linha_digitavel_normal_2 varchar
        
                , cod_parcela_normal_3 varchar
                , valor_normal_3 varchar
                , vencimento_normal_3 varchar
                , numeracao_normal_3 varchar
                , nosso_numero_normal_3 varchar
                , codigo_barra_normal_3 varchar
                , linha_digitavel_normal_3 varchar
        
                , cod_parcela_normal_4 varchar
                , valor_normal_4 varchar
                , vencimento_normal_4 varchar
                , numeracao_normal_4 varchar
                , nosso_numero_normal_4 varchar
                , codigo_barra_normal_4 varchar
                , linha_digitavel_normal_4 varchar
        
                , cod_parcela_normal_5 varchar
                , valor_normal_5 varchar
                , vencimento_normal_5 varchar
                , numeracao_normal_5 varchar
                , nosso_numero_normal_5 varchar
                , codigo_barra_normal_5 varchar
                , linha_digitavel_normal_5 varchar
        
                , cod_parcela_normal_6 varchar
                , valor_normal_6 varchar
                , vencimento_normal_6 varchar
                , numeracao_normal_6 varchar
                , nosso_numero_normal_6 varchar
                , codigo_barra_normal_6 varchar
                , linha_digitavel_normal_6 varchar
        
                , cod_parcela_normal_7 varchar
                , valor_normal_7 varchar
                , vencimento_normal_7 varchar
                , numeracao_normal_7 varchar
                , nosso_numero_normal_7 varchar
                , codigo_barra_normal_7 varchar
                , linha_digitavel_normal_7 varchar
        
                , cod_parcela_normal_8 varchar
                , valor_normal_8 varchar
                , vencimento_normal_8 varchar
                , numeracao_normal_8 varchar
                , nosso_numero_normal_8 varchar
                , codigo_barra_normal_8 varchar
                , linha_digitavel_normal_8 varchar
        
                , cod_parcela_normal_9 varchar
                , valor_normal_9 varchar
                , vencimento_normal_9 varchar
                , numeracao_normal_9 varchar
                , nosso_numero_normal_9 varchar
                , codigo_barra_normal_9 varchar
                , linha_digitavel_normal_9 varchar
        
                , cod_parcela_normal_10 varchar
                , valor_normal_10 varchar
                , vencimento_normal_10 varchar
                , numeracao_normal_10 varchar
                , nosso_numero_normal_10 varchar
                , codigo_barra_normal_10 varchar
                , linha_digitavel_normal_10 varchar
        
                , cod_parcela_normal_11 varchar
                , valor_normal_11 varchar
                , vencimento_normal_11 varchar
                , numeracao_normal_11 varchar
                , nosso_numero_normal_11 varchar
                , codigo_barra_normal_11 varchar
                , linha_digitavel_normal_11 varchar
        
                , cod_parcela_normal_12 varchar
                , valor_normal_12 varchar
                , vencimento_normal_12 varchar
                , numeracao_normal_12 varchar
                , nosso_numero_normal_12 varchar
                , codigo_barra_normal_12 varchar
                , linha_digitavel_normal_12 varchar
        
                , soma_creditos varchar
        
                , cod_credito_1 varchar
                , descricao_1 varchar
                , valor_1 varchar
        
                , cod_credito_2 varchar
                , descricao_2 varchar
                , valor_2 varchar
        
                , cod_credito_3 varchar
                , descricao_3 varchar
                , valor_3 varchar
        
                , cod_credito_4 varchar
                , descricao_4 varchar
                , valor_4 varchar
        
                , cod_credito_5 varchar
                , descricao_5 varchar
                , valor_5 varchar
        
                , cod_credito_6 varchar
                , descricao_6 varchar
                , valor_6 varchar
        
                , cod_credito_7 varchar
                , descricao_7 varchar
                , valor_7 varchar
        
                , valor_venal_territorial varchar
                , valor_venal_predial varchar
                , valor_venal_total varchar
        
                , valor_m2_territorial varchar
                , valor_m2_predial varchar
        
                , localizacao_primeiro_nivel varchar
                , valor_imposto varchar
        
                , area_limpeza varchar
                , aliquota_limpeza varchar
        
                , aliquota_imposto varchar
        
                , atributo_1 varchar
                , atributo_2 varchar
                , atributo_3 varchar
                , atributo_4 varchar
                , atributo_5 varchar
                , atributo_6 varchar
                , atributo_7 varchar
                , atributo_8 varchar
                , atributo_9 varchar
                , atributo_10 varchar
                , atributo_11 varchar
                , atributo_12 varchar
                , atributo_13 varchar
                , atributo_14 varchar
                , atributo_15 varchar
                , valor_m2_predial_descoberto varchar
                , valor_venal_predial_descoberto varchar
                , area_construida_total varchar
                , area_descoberta varchar
                , valor_venal_predial_coberto varchar
                                                                                    \r\n";
    $stSql .= " )                                                                   \r\n";
    
    return $stSql;
}

function montaExecutaFuncaoEconomica()
{
    $stSql  = " SELECT  *  \r\n";
    $stSql .= "   FROM  arrecadacao.fn_lista_emissao_grafica_economico (

                '".$this->stTipoInscricao."'

                , '".$this->inExercicio."'
                , ".$this->inCodGrupo."

                , ".$this->inCodCredito."
                , ".$this->inCodEspecie."
                , ".$this->inCodGenero."
                , ".$this->inCodNatureza."

                , '".$this->inCodIEInicial."'
                , ".(isset($this->inCodIEFinal) ? $this->inCodIEFinal : 0)."

                , '".$this->stLocalizacaoInicial."'
                , '".$this->stLocalizacaoFinal."'
                , ".$this->inCodEnderecoInicial."
                , ".$this->inCodEnderecoFinal."

                , '".$this->stOrdemEmissao."'
                , '".$this->stOrdemLote."'
                , '".$this->stOrdemImovel."'
                , '".$this->stOrdemEdificacao."'
                , '".$this->stPadraoCodBarra."'

                ) AS lista_emissao (      \r\n";
    $stSql .= "

          inscricao int
        , exercicio int

        , cod_lancamento  int
        , lancamento_vencimento date
        , lancamento_valor numeric
        , cod_grupo int
        , nom_grupo varchar
        , numcgm int
        , nom_cgm varchar

        , inscricao_municipal_economica int
        , codigo_composto varchar
        , nom_localizacao varchar
        , cod_lote int
        , nom_fantasia varchar
        , cnpj varchar

        , nom_tipo_logradouro varchar
        , cod_logradouro int
        , nom_logradouro varchar
        , numero varchar
        , complemento varchar
        , nom_bairro varchar
        , cep varchar
        , cod_municipio varchar
        , nom_municipio varchar
        , cod_uf varchar
        , sigla_uf varchar

        , c_nom_tipo_logradouro varchar
        , c_cod_logradouro int
        , c_nom_logradouro varchar
        , c_numero varchar
        , c_complemento varchar
        , c_nom_bairro varchar
        , c_cep varchar
        , c_cod_municipio varchar
        , c_nom_municipio varchar
        , c_cod_uf varchar
        , c_sigla_uf varchar
        , c_caixa_postal varchar

        , qtde_parcelas_unicas varchar

        , cod_parcela_unica_1 varchar
        , valor_unica_1 varchar
        , vencimento_unica_1 varchar
        , desconto_unica_1 varchar
        , numeracao_unica_1 varchar
        , nosso_numero_unica_1 varchar
        , codigo_barra_unica_1 varchar
        , linha_digitavel_unica_1 varchar

        , cod_parcela_unica_2 varchar
        , valor_unica_2 varchar
        , vencimento_unica_2 varchar
        , desconto_unica_2 varchar
        , numeracao_unica_2 varchar
        , nosso_numero_unica_2 varchar
        , codigo_barra_unica_2 varchar
        , linha_digitavel_unica_2 varchar

        , cod_parcela_unica_3 varchar
        , valor_unica_3 varchar
        , vencimento_unica_3 varchar
        , desconto_unica_3 varchar
        , numeracao_unica_3 varchar
        , nosso_numero_unica_3 varchar
        , codigo_barra_unica_3 varchar
        , linha_digitavel_unica_3 varchar

        , cod_parcela_unica_4 varchar
        , valor_unica_4 varchar
        , vencimento_unica_4 varchar
        , desconto_unica_4 varchar
        , numeracao_unica_4 varchar
        , nosso_numero_unica_4 varchar
        , codigo_barra_unica_4 varchar
        , linha_digitavel_unica_4 varchar

        , cod_parcela_unica_5 varchar
        , valor_unica_5 varchar
        , vencimento_unica_5 varchar
        , desconto_unica_5 varchar
        , numeracao_unica_5 varchar
        , nosso_numero_unica_5 varchar
        , codigo_barra_unica_5 varchar
        , linha_digitavel_unica_5 varchar

        , qtde_parcelas_normais varchar

        , cod_parcela_normal_1 varchar
        , valor_normal_1 varchar
        , vencimento_normal_1 varchar
        , numeracao_normal_1 varchar
        , nosso_numero_normal_1 varchar
        , codigo_barra_normal_1 varchar
        , linha_digitavel_normal_1 varchar

        , cod_parcela_normal_2 varchar
        , valor_normal_2 varchar
        , vencimento_normal_2 varchar
        , numeracao_normal_2 varchar
        , nosso_numero_normal_2 varchar
        , codigo_barra_normal_2 varchar
        , linha_digitavel_normal_2 varchar

        , cod_parcela_normal_3 varchar
        , valor_normal_3 varchar
        , vencimento_normal_3 varchar
        , numeracao_normal_3 varchar
        , nosso_numero_normal_3 varchar
        , codigo_barra_normal_3 varchar
        , linha_digitavel_normal_3 varchar

        , cod_parcela_normal_4 varchar
        , valor_normal_4 varchar
        , vencimento_normal_4 varchar
        , numeracao_normal_4 varchar
        , nosso_numero_normal_4 varchar
        , codigo_barra_normal_4 varchar
        , linha_digitavel_normal_4 varchar

        , cod_parcela_normal_5 varchar
        , valor_normal_5 varchar
        , vencimento_normal_5 varchar
        , numeracao_normal_5 varchar
        , nosso_numero_normal_5 varchar
        , codigo_barra_normal_5 varchar
        , linha_digitavel_normal_5 varchar

        , cod_parcela_normal_6 varchar
        , valor_normal_6 varchar
        , vencimento_normal_6 varchar
        , numeracao_normal_6 varchar
        , nosso_numero_normal_6 varchar
        , codigo_barra_normal_6 varchar
        , linha_digitavel_normal_6 varchar

        , cod_parcela_normal_7 varchar
        , valor_normal_7 varchar
        , vencimento_normal_7 varchar
        , numeracao_normal_7 varchar
        , nosso_numero_normal_7 varchar
        , codigo_barra_normal_7 varchar
        , linha_digitavel_normal_7 varchar

        , cod_parcela_normal_8 varchar
        , valor_normal_8 varchar
        , vencimento_normal_8 varchar
        , numeracao_normal_8 varchar
        , nosso_numero_normal_8 varchar
        , codigo_barra_normal_8 varchar
        , linha_digitavel_normal_8 varchar

        , cod_parcela_normal_9 varchar
        , valor_normal_9 varchar
        , vencimento_normal_9 varchar
        , numeracao_normal_9 varchar
        , nosso_numero_normal_9 varchar
        , codigo_barra_normal_9 varchar
        , linha_digitavel_normal_9 varchar

        , cod_parcela_normal_10 varchar
        , valor_normal_10 varchar
        , vencimento_normal_10 varchar
        , numeracao_normal_10 varchar
        , nosso_numero_normal_10 varchar
        , codigo_barra_normal_10 varchar
        , linha_digitavel_normal_10 varchar

        , cod_parcela_normal_11 varchar
        , valor_normal_11 varchar
        , vencimento_normal_11 varchar
        , numeracao_normal_11 varchar
        , nosso_numero_normal_11 varchar
        , codigo_barra_normal_11 varchar
        , linha_digitavel_normal_11 varchar

        , cod_parcela_normal_12 varchar
        , valor_normal_12 varchar
        , vencimento_normal_12 varchar
        , numeracao_normal_12 varchar
        , nosso_numero_normal_12 varchar
        , codigo_barra_normal_12 varchar
        , linha_digitavel_normal_12 varchar

        --, qtde_creditos varchar
        , soma_creditos varchar

        , cod_credito_1 varchar
        , descricao_1 varchar
        , valor_1 varchar

        , cod_credito_2 varchar
        , descricao_2 varchar
        , valor_2 varchar

        , cod_credito_3 varchar
        , descricao_3 varchar
        , valor_3 varchar

        , cod_credito_4 varchar
        , descricao_4 varchar
        , valor_4 varchar

        , cod_credito_5 varchar
        , descricao_5 varchar
        , valor_5 varchar

        , cod_credito_6 varchar
        , descricao_6 varchar
        , valor_6 varchar

        , cod_credito_7 varchar
        , descricao_7 varchar
        , valor_7 varchar

        , localizacao_primeiro_nivel varchar
        , lista_atividades varchar
        , lista_responsaveis varchar

        , atributo_1 varchar
        , atributo_2 varchar
        , atributo_3 varchar
        , atributo_4 varchar
        , atributo_5 varchar
        , atributo_6 varchar
        , atributo_7 varchar
        , atributo_8 varchar
        , atributo_9 varchar
        , atributo_10 varchar
        , atributo_11 varchar
        , atributo_12 varchar
        , atributo_13 varchar
        , atributo_14 varchar
        , atributo_15 varchar

        , lote VARCHAR
        , quadra VARCHAR

        , inscricao_economica VARCHAR
        , data_abertura VARCHAR
        , numcgm_responsavel  VARCHAR
        , nome_responsavel VARCHAR
        , cod_natureza VARCHAR
        , natureza_juridica VARCHAR
        , cod_categoria VARCHAR
        , categoria VARCHAR
        , cod_atividade_principal VARCHAR
        , descricao_atividade_principal VARCHAR
        , data_inicio VARCHAR

        -- RELACAO SOCIOS
        , numcgm_socio_1 VARCHAR
        , nome_socio_1 VARCHAR
        , quota_socio_1 VARCHAR

        , numcgm_socio_2 VARCHAR
        , nome_socio_2 VARCHAR
        , quota_socio_2 VARCHAR

        , numcgm_socio_3 VARCHAR
        , nome_socio_3 VARCHAR
        , quota_socio_3 VARCHAR

        , numcgm_socio_4 VARCHAR
        , nome_socio_4 VARCHAR
        , quota_socio_4 VARCHAR

        , numcgm_socio_5 VARCHAR
        , nome_socio_5 VARCHAR
        , quota_socio_5 VARCHAR

                                                                    \r\n";

    $stSql .= " )                                                   \r\n";

    return $stSql;
}

}
?>

