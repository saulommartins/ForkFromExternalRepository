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
    * Data de Criacao: 17/07/2007

    * @author Analista      : Fábio Bertoldi Rodrigues
    * @author Desenvolvedor : Rodrigo D.S.
    * @ignore

    * $Id: TTipoFiscalizacao.class.php 29237 2008-04-16 12:02:48Z fabio $

    *Casos de uso: uc-05.07.02

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE 								 	                                   );

class TFISTipoFiscalizacao extends Persistente
{
/**
    * Metodo Construtor
    * @access Private
*/
function TFISTipoFiscalizacao()
{
    parent::Persistente();
    $this->setTabela( 'fiscalizacao.tipo_fiscalizacao' );

    $this->setCampoCod( 'cod_tipo');

    $this->AddCampo( 'cod_tipo','integer',true,'',true,false     		);
    $this->AddCampo( 'descricao','varchar',true,'40',false,false 	);
}

function recuperaTipoFiscalizacao(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
{
    $obErro  = new Erro;
    $obConexao  = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaTipoFiscalizacao().$stCondicao.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

//RECUPERA SOMENTE OS 2 CAMPOS DOS TIPOS DE FISCALIZACAO
function montaRecuperaTipoFiscalizacao()
{
    $stSQL = "SELECT * FROM fiscalizacao.tipo_fiscalizacao";

    return $stSQL;
}
}// fecha classe de mapeamento
