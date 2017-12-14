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
* Classe de Mapeamento para a tabela sw_documento_assunto
* Data de Criação: 01/09/2006

* @author Analista: Cassiano de Vasconcellos Ferreira
* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

$Revision: 17314 $
$Name$
$Author: cassiano $
$Date: 2006-10-31 09:27:43 -0300 (Ter, 31 Out 2006) $

Casos de uso: uc-01.06.96
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPRODocumentoAssunto extends Persistente
{
function TPRODocumentoAssunto()
{
    parent::Persistente();
    $this->setTabela('sw_documento_assunto');

    $this->AddCampo('cod_documento',	'integer',true,	'',false,'TPRODocumento');
    $this->AddCampo('cod_classificacao','integer',true,	'',false,'TPROAssunto');
    $this->AddCampo('cod_assunto',		'integer',true,	'',false,'TPROAssunto');

}

function montaRecuperaRelacionamento()
{
    $stSql  = " SELECT                                                               \n";
    $stSql .= "      SW_DOCUMENTO.cod_documento,                                     \n";
    $stSql .= "      SW_DOCUMENTO.nom_documento,                                     \n";
    $stSql .= "      SW_DOCUMENTO_ASSUNTO.cod_classificacao,                         \n";
    $stSql .= "      SW_DOCUMENTO_ASSUNTO.cod_assunto                                \n";
    $stSql .= " FROM                                                                 \n";
    $stSql .= "      SW_DOCUMENTO,                                                   \n";
    $stSql .= "      SW_DOCUMENTO_ASSUNTO                                            \n";
    $stSql .= " WHERE                                                                \n";
    $stSql .= "      SW_DOCUMENTO.cod_documento = SW_DOCUMENTO_ASSUNTO.cod_documento \n";

    return $stSql;
}

}
