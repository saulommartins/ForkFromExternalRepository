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
    * Classe de mapeamento da tabela ARRECADACAO.MODELO_CARNE
    * Data de Criação: 29/09/2008

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: $

* Casos de uso: uc-05.03.1
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TARRModeloCarne extends Persistente
{
    /**
        * Método Construtor
        * @access Public
    */
    public function TARRModeloCarne()
    {
        parent::Persistente();
        $this->setTabela('arrecadacao.modelo_carne');

        $this->setCampoCod('cod_modelo');
        $this->setComplementoChave('');

        $this->AddCampo('cod_modelo', 'integer', true, '', true, false );
        $this->AddCampo('nom_modelo', 'varchar', false, '80', false, false );
        $this->AddCampo('nom_arquivo', 'varchar', false, '80', false, false );
        $this->AddCampo('cod_modulo', 'integer', false, '', false, true );
        $this->AddCampo('capa_primeira_folha', 'boolean', false, '', false, false );
    }

    public function recuperaListaModeloCarneLayout(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaListaModeloCarneLayout().$stFiltro;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql,  $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaModeloCarneLayout()
    {
        $stSql = "
            SELECT
                modelo_carne.cod_modelo,
                modelo_carne.nom_modelo,
                modelo_carne.cod_modulo,
                modelo_carne.capa_primeira_folha

            FROM
                arrecadacao.modelo_carne

            WHERE
                modelo_carne.nom_arquivo = 'RCarneDiversosLayoutUrbem.class.php'
        ";

        return $stSql;
    }

    public function recuperaAtributosDinamicos(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaAtributosDinamicos().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql,  $boTransacao );

        return $obErro;
    }

    public function montaRecuperaAtributosDinamicos()
    {
        $stSql = "
            SELECT
                atributo_dinamico.cod_modulo,
                atributo_dinamico.cod_cadastro,
                atributo_dinamico.cod_atributo,
                atributo_dinamico.nom_atributo

            FROM
                administracao.atributo_dinamico
        ";

        return $stSql;
    }

    public function recuperaLarguraAtributosDinamicosImovel(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaLarguraAtributosDinamicosImovel().$stFiltro;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql,  $boTransacao );

        return $obErro;
    }

    public function montaRecuperaLarguraAtributosDinamicosImovel()
    {
        $stSql = "
            SELECT
                CASE WHEN ( COALESCE( max( char_length ( COALESCE( atributo_imovel_valor.valor, atributo_lote_urbano_valor.valor, atributo_lote_rural_valor.valor, atributo_tipo_edificacao_valor.valor ) ) ), 0 ) > COALESCE( max( char_length ( atributo_valor_padrao.valor_padrao ) ), 0 ) ) THEN
                    CASE WHEN ( max( char_length(atributo_dinamico.nom_atributo) ) > COALESCE( max( char_length ( COALESCE( atributo_imovel_valor.valor, atributo_lote_urbano_valor.valor, atributo_lote_rural_valor.valor, atributo_tipo_edificacao_valor.valor ) ) ), 0 ) ) THEN
                        max( char_length(atributo_dinamico.nom_atributo) )
                    ELSE
                        max( char_length ( COALESCE( atributo_imovel_valor.valor, atributo_lote_urbano_valor.valor, atributo_lote_rural_valor.valor, atributo_tipo_edificacao_valor.valor ) ) )
                    END
                ELSE
                    CASE WHEN ( max( char_length(atributo_dinamico.nom_atributo) ) > COALESCE( max( char_length ( atributo_valor_padrao.valor_padrao ) ), 0 ) ) THEN
                        max( char_length(atributo_dinamico.nom_atributo) )
                    ELSE
                        max( char_length ( atributo_valor_padrao.valor_padrao ) )
                    END
                END*2 AS largura

            FROM
                administracao.atributo_dinamico

            LEFT JOIN
                imobiliario.atributo_imovel_valor
            ON
                atributo_imovel_valor.cod_modulo = atributo_dinamico.cod_modulo
                AND atributo_imovel_valor.cod_atributo = atributo_dinamico.cod_atributo
                AND atributo_imovel_valor.cod_cadastro = atributo_dinamico.cod_cadastro

            LEFT JOIN
                imobiliario.atributo_lote_urbano_valor
            ON
                atributo_lote_urbano_valor.cod_modulo = atributo_dinamico.cod_modulo
                AND atributo_lote_urbano_valor.cod_atributo = atributo_dinamico.cod_atributo
                AND atributo_lote_urbano_valor.cod_cadastro = atributo_dinamico.cod_cadastro

            LEFT JOIN
                imobiliario.atributo_lote_rural_valor
            ON
                atributo_lote_rural_valor.cod_modulo = atributo_dinamico.cod_modulo
                AND atributo_lote_rural_valor.cod_atributo = atributo_dinamico.cod_atributo
                AND atributo_lote_rural_valor.cod_cadastro = atributo_dinamico.cod_cadastro

            LEFT JOIN
                imobiliario.atributo_tipo_edificacao_valor
            ON
                atributo_tipo_edificacao_valor.cod_modulo = atributo_dinamico.cod_modulo
                AND atributo_tipo_edificacao_valor.cod_atributo = atributo_dinamico.cod_atributo
                AND atributo_tipo_edificacao_valor.cod_cadastro = atributo_dinamico.cod_cadastro

            LEFT JOIN
                administracao.atributo_valor_padrao
            ON
                atributo_valor_padrao.cod_modulo = atributo_dinamico.cod_modulo
                AND atributo_valor_padrao.cod_atributo = atributo_dinamico.cod_atributo
                AND atributo_valor_padrao.cod_cadastro = atributo_dinamico.cod_cadastro
                AND atributo_valor_padrao.cod_valor = COALESCE( atributo_imovel_valor.valor, atributo_lote_urbano_valor.valor, atributo_lote_rural_valor.valor, atributo_tipo_edificacao_valor.valor )
        ";

        return $stSql;
    }

    public function recuperaLarguraAtributosDinamicosEmpresa(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaLarguraAtributosDinamicosEmpresa().$stFiltro;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql,  $boTransacao );

        return $obErro;
    }

    public function montaRecuperaLarguraAtributosDinamicosEmpresa()
    {
        $stSql = "
            SELECT
                CASE WHEN ( COALESCE( max( char_length ( COALESCE( atributo_elem_cad_economico_valor.valor, atributo_empresa_fato_valor.valor, atributo_cad_econ_autonomo_valor.valor, atributo_empresa_direito_valor.valor ) ) ), 0 ) > COALESCE( max( char_length ( atributo_valor_padrao.valor_padrao ) ), 0 ) ) THEN
                    CASE WHEN ( max( char_length(atributo_dinamico.nom_atributo) ) > COALESCE( max( char_length ( COALESCE( atributo_elem_cad_economico_valor.valor, atributo_empresa_fato_valor.valor, atributo_cad_econ_autonomo_valor.valor, atributo_empresa_direito_valor.valor ) ) ), 0) ) THEN
                        max( char_length(atributo_dinamico.nom_atributo) )
                    ELSE
                        max( char_length ( COALESCE( atributo_elem_cad_economico_valor.valor, atributo_empresa_fato_valor.valor, atributo_cad_econ_autonomo_valor.valor, atributo_empresa_direito_valor.valor ) ) )
                    END
                ELSE
                    CASE WHEN ( COALESCE( max( char_length(atributo_dinamico.nom_atributo) ), 0 ) > COALESCE( max( COALESCE( char_length ( atributo_valor_padrao.valor_padrao ), 0 ) ),0 ) ) THEN
                        max( char_length(atributo_dinamico.nom_atributo) )
                    ELSE
                        max( char_length ( atributo_valor_padrao.valor_padrao ) )
                    END
                END*2 AS largura

            FROM
                administracao.atributo_dinamico

            LEFT JOIN
                economico.atributo_elem_cad_economico_valor
            ON
                atributo_elem_cad_economico_valor.cod_modulo = atributo_dinamico.cod_modulo
                AND atributo_elem_cad_economico_valor.cod_atributo = atributo_dinamico.cod_atributo
                AND atributo_elem_cad_economico_valor.cod_cadastro = atributo_dinamico.cod_cadastro

            LEFT JOIN
                economico.atributo_empresa_fato_valor
            ON
                atributo_empresa_fato_valor.cod_modulo = atributo_dinamico.cod_modulo
                AND atributo_empresa_fato_valor.cod_atributo = atributo_dinamico.cod_atributo
                AND atributo_empresa_fato_valor.cod_cadastro = atributo_dinamico.cod_cadastro

            LEFT JOIN
                economico.atributo_cad_econ_autonomo_valor
            ON
                atributo_cad_econ_autonomo_valor.cod_modulo = atributo_dinamico.cod_modulo
                AND atributo_cad_econ_autonomo_valor.cod_atributo = atributo_dinamico.cod_atributo
                AND atributo_cad_econ_autonomo_valor.cod_cadastro = atributo_dinamico.cod_cadastro

            LEFT JOIN
                economico.atributo_empresa_direito_valor
            ON
                atributo_empresa_direito_valor.cod_modulo = atributo_dinamico.cod_modulo
                AND atributo_empresa_direito_valor.cod_atributo = atributo_dinamico.cod_atributo
                AND atributo_empresa_direito_valor.cod_cadastro = atributo_dinamico.cod_cadastro

            LEFT JOIN
                administracao.atributo_valor_padrao
            ON
                atributo_valor_padrao.cod_modulo = atributo_dinamico.cod_modulo
                AND atributo_valor_padrao.cod_atributo = atributo_dinamico.cod_atributo
                AND atributo_valor_padrao.cod_cadastro = atributo_dinamico.cod_cadastro
                AND atributo_valor_padrao.cod_valor = COALESCE( atributo_elem_cad_economico_valor.valor, atributo_empresa_fato_valor.valor, atributo_cad_econ_autonomo_valor.valor, atributo_empresa_direito_valor.valor )
        ";

        return $stSql;
    }

}
?>
