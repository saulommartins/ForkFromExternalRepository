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
    * Classe de regra de mapeamento para FISCALIZACAO.DOCUMENTO
    * Data de Criacao: 25/07/2008

    * @author Analista      : Heleno Menezes dos Santos
    * @author Desenvolvedor : Jânio Eduardo
    * @ignore

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE );

class TFISDocumento extends Persistente
{
/**
    * Metodo Construtor
    * @access Private
*/

function TFISDocumento()
{
    parent::Persistente();
    $this->setTabela('fiscalizacao.documento');

    $this->setCampoCod('cod_documento');
    $this->setComplementoChave('cod_tipo_fiscalizacao');

    $this->AddCampo( 'cod_documento','interger',true,'',true,false        );
    $this->AddCampo( 'cod_tipo_fiscalizacao','integer',true,'',false,true        );
    $this->AddCampo( 'nom_documento','text',true,'',false,false        );
    $this->AddCampo( 'uso_interno','integer',true,'',false,false );
    $this->AddCampo( 'ativo','boolean',true,'',false,false        );
}

function recuperaDocumento(&$rsRecordSet, $stCondicao, $stOrdem = "" , $boTransacao = "")
{
            $obErro      = new Erro;
        $obConexao   = new Conexao;
            $rsRecordSet = new RecordSet;

            $stSql = $this->montaRecuperaListaDocumento().$stCondicao.$stOrdem;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao);

        return $obErro;
}

function montaRecuperaListaDocumento()
{
         $stSql =" SELECT documento.cod_documento                                       \n";
             $stSql.="       ,documento.cod_tipo_fiscalizacao                               \n";
         $stSql.="       ,documento.nom_documento                                       \n";
         $stSql.="       ,documento.uso_interno,                                        \n";
                 $stSql.="   CASE documento.ativo                                               \n";
                 $stSql.="        WHEN true  THEN 'Ativo'                                       \n";
                 $stSql.="        WHEN false THEN 'Inativo'                                     \n";
                 $stSql.="    END AS ativo                                                      \n";
             $stSql.="       ,tipo_fiscalizacao.descricao                                   \n";
             $stSql.="   FROM fiscalizacao.documento                                        \n";
             $stSql.="       ,fiscalizacao.tipo_fiscalizacao                                \n";
             $stSql.="  WHERE documento.cod_tipo_fiscalizacao = tipo_fiscalizacao.cod_tipo  \n";

    return $stSql;
}

function recuperaDadosGenericosConfiguracaoSW(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDadosGenericosConfiguracaoSW();
    $this->setDebug( $stSql );
    //$this->debug();exit;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosGenericosConfiguracaoSW()
{
    $stSql = "
        SELECT
            (
                SELECT
                    '".URBEM_ROOT_URL."'||'/gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/'||valor
                FROM
                    administracao.configuracao
                WHERE
                    cod_modulo = 2
                    AND exercicio = extract(year from now())
                    AND parametro = 'logotipo'
            )AS url_logo,
            (

                SELECT
                sw_municipio.nom_municipio

                FROM
                    sw_municipio

                WHERE
                    sw_municipio.cod_municipio IN (
                        SELECT
                            valor
                        FROM
                            administracao.configuracao

                        WHERE
                            cod_modulo = 2
                            AND exercicio = extract(year from now())
                            AND parametro = 'cod_municipio'
                    )AND sw_municipio.cod_uf IN (
                        SELECT
                            valor
                        FROM
                            administracao.configuracao

                        WHERE
                            cod_modulo = 2
                            AND exercicio = extract(year from now())
                            AND parametro = 'cod_uf'
                    )
            ) AS nom_mun,
            (
                SELECT
                    valor
                FROM
                    administracao.configuracao
                WHERE
                    cod_modulo = 2
                    AND exercicio = extract(year from now())
                    AND parametro = 'nom_prefeitura'
            ) AS nom_pref
    ";

    return $stSql;
}

}// fecha classe de mapeamento
