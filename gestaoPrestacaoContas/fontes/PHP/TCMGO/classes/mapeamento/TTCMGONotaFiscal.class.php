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

/*
    * Classe de mapeamento da tabela tcmgo.nota_fiscal
    * Data de Criação   : 23/09/2008

    * @author Analista      Tonismar Bernardo
    * @author Desenvolvedor Alexandre Melo

    * @package URBEM
    * @subpackage

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCMGONotaFiscal extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTCMGONotaFiscal()
{
    parent::Persistente();
    $this->setTabela("tcmgo.nota_fiscal");

    $this->setCampoCod('cod_nota');
    $this->setComplementoChave('');

    $this->AddCampo( 'cod_nota'            , 'integer' , true  , ''     , true  , false  );
    $this->AddCampo( 'nro_nota'            , 'integer' , false , ''     , false , false  );
    $this->AddCampo( 'nro_serie'           , 'varchar' , false , ''     , false , false  );
    $this->AddCampo( 'aidf'                , 'char'    , true  , ''     , false , false  );
    $this->AddCampo( 'data_emissao'        , 'date'    , true  , ''     , false , false  );
    $this->AddCampo( 'vl_nota'             , 'numeric' , true  , ''     , false , false  );
    $this->AddCampo( 'inscricao_municipal' , 'bigint'  , false , ''     , false , false  );
    $this->AddCampo( 'inscricao_estadual'  , 'bigint'  , false , ''     , false , false  );
    $this->AddCampo( 'cod_tipo'            , 'integer' , true  , ''     , true  , false  );
    $this->AddCampo( 'chave_acesso'        , 'numeric' , false , ''     , false , false  );

}

function recuperaNotasFiscais(&$rsRecordSet, $stFiltro, $stOrdem, $boTransacao)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaNotasFiscais().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );

    return $obErro;
}

function montaRecuperaNotasFiscais()
{
    $stSql  = " SELECT nota_fiscal.cod_nota
                    , nota_fiscal.nro_nota
                    , nota_fiscal.nro_serie
                    , nota_fiscal.aidf
                    , to_char(data_emissao, 'dd/mm/yyyy') as data_emissao
                    , publico.fn_numeric_br(vl_nota) as vl_nota
                    , nota_fiscal.inscricao_municipal
                    , nota_fiscal.inscricao_estadual
                    , nota_fiscal.cod_tipo
                    , nota_fiscal.chave_acesso
                    FROM tcmgo.nota_fiscal
                    LEFT JOIN tcmgo.nota_fiscal_empenho_liquidacao
                        ON(nota_fiscal_empenho_liquidacao.cod_nota = nota_fiscal.cod_nota)
    ";

    return $stSql;
}

}//fimclasse

?>
