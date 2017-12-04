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
  * Classe de mapeamento da tabela ECONOMICO.TIPO_LICENCA_MODELO_DOCUMENTO
  * Data de Criação: 09/10/2006

  * @author Analista: Fabio Bertoldi
  * @author Desenvolvedor: Diego Bueno Coelho

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TCEMTipoLicencaModeloDocumento.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.02.12
*/

/*
$Log$
Revision 1.2  2007/05/11 20:31:54  dibueno
Alterações para possibilitar a emissao do alvará

Revision 1.1  2006/10/11 10:29:32  dibueno
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TCEMTipoLicencaModeloDocumento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCEMTipoLicencaModeloDocumento()
{
    parent::Persistente();
    $this->setTabela('economico.tipo_licenca_modelo_documento');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_tipo, cod_tipo_documento');

    $this->AddCampo('cod_tipo','integer',true,'',true,true);
    $this->AddCampo('cod_tipo_documento','integer',true,'',true,true);
    $this->AddCampo('cod_documento','integer',true,'',true,true);
    $this->AddCampo('timestamp','timestamp',false,'',false,false);

}

function recuperaLicencaDiversaModeloDocumento(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaLicencaDiversaModeloDocumento().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
   // $this->debug();
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaLicencaDiversaModeloDocumento()
{
    $stSQL  =" SELECT                                                           \n";
    $stSQL .="      etlmd.cod_tipo  as cod_tipo                                            \n";
    $stSQL .="      , amd.*                                                     \n";
    $stSQL .=" FROM                                                             \n";
    /*$stSQL .="      economico.tipo_licenca_modelo_documento  as etlmd           \n";
    $stSQL .="      INNER JOIN administracao.modelo_documento as amd            \n";*/
    $stSQL .="      administracao.modelo_documento as amd            		\n";
    $stSQL .="      LEFT JOIN economico.tipo_licenca_modelo_documento  as etlmd \n";
    $stSQL .="      ON amd.cod_tipo_documento = etlmd.cod_tipo_documento        \n";
    $stSQL .="      AND amd.cod_documento = etlmd.cod_documento                 \n";

    return $stSQL;

}

}
