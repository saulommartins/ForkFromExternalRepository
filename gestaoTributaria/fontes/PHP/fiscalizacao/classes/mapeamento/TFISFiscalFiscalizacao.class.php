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
    * Classe de regra de mapeamento para FISCALIZACAO.TIPO_FISCALIZACAO
    * Data de Criacao: 20/07/2007

    * @author Analista      : Fábio Bertoldi Rodrigues
    * @author Desenvolvedor : Rodrigo D.S.
    * @ignore

    * $Id: TFiscalFiscalizacao.class.php 29237 2008-04-16 12:02:48Z fabio $

    *Casos de uso: uc-05.07.02

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE                                                                      );

class TFISFiscalFiscalizacao extends Persistente
{
/**
    * Metodo Construtor
    * @access Private
*/
function TFISFiscalFiscalizacao()
{
    parent::Persistente();
    $this->setTabela( 'fiscalizacao.fiscal_fiscalizacao' );

    $this->setCampoCod( 'cod_fiscal' );
    $this->setComplementoChave( 'cod_tipo' );

    $this->AddCampo( 'cod_fiscal','integer',true,'',true,false  );
    $this->AddCampo( 'cod_tipo','integer',true,'',true,false    );
}

function recuperaListaFiscalFiscalizacao(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaListaFiscalFiscalizacao().$stCondicao.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaListaFiscalFiscalizacao()
{
    $stSql = " SELECT tipo_fiscalizacao.cod_tipo                                    \n";
    $stSql.= "       ,tipo_fiscalizacao.descricao                                   \n";
    $stSql.= "   FROM fiscalizacao.fiscal_fiscalizacao                              \n";
    $stSql.= "       ,fiscalizacao.tipo_fiscalizacao                                \n";
    $stSql.= "  WHERE fiscal_fiscalizacao.cod_tipo = tipo_fiscalizacao.cod_tipo     \n";

    return $stSql;
}

}// fecha classe de mapeamento
