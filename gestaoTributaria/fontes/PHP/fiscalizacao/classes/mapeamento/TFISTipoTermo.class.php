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
    * Classe de regra de mapeamento para FISCALIZACAO.TIPO_TERMO
    * Data de Criacao: 11/11/2008

    * @author Analista      : Heleno Santos
    * @author Desenvolvedor : Marcio Medeiros
    * @ignore

    * $Id: TFISTipoTermo.class.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso:

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE 								 	                                   );

class TFISTipoTermo extends Persistente
{
/**
    * Metodo Construtor
    * @access Private
*/
function __construct()
{
    parent::Persistente();
    $this->setTabela( 'fiscalizacao.tipo_termo' );

    $this->setCampoCod( 'cod_termo');

    $this->AddCampo( 'cod_termo','integer',true,'',true,false     );
    $this->AddCampo( 'nom_termo','varchar',true,'25',false,false  );
}

function recuperaTipoTermo(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    return $this->executaRecupera("montaRecuperaTipoTermo", $rsRecordSet, $stFiltro, $stOrdem, $boTransacao);
/**
    $obErro  = new Erro;
    $obConexao  = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaTipoTermo().$stCondicao.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;

 */
}

//RECUPERA SOMENTE OS 2 CAMPOS DOS TIPOS DE TERMO
function montaRecuperaTipoTermo()
{
    $stSQL = "SELECT * FROM fiscalizacao.tipo_termo";

    return $stSQL;
}
}// fecha classe de mapeamento
?>
