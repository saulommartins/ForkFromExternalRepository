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
* Classe de Mapeamento para a tabela sw_copia_digital
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 28934 $
$Name$
$Author: rodrigosoares $
$Date: 2008-04-01 18:52:01 -0300 (Ter, 01 Abr 2008) $

Casos de uso: uc-01.06.98
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPROCopiaDigital extends Persistente
{
function TPROCopiaDigital()
{
    parent::Persistente();
    $this->setTabela('sw_copia_digital');
    $this->setCampoCod('cod_copia');
    $this->setComplementoChave('cod_documento,cod_processo,exercicio');

    $this->AddCampo('cod_copia',    'integer', true,'', true,false);
    $this->AddCampo('cod_documento','integer', true,'', true,true);
    $this->AddCampo('cod_processo', 'integer', true,'', true,true);
    $this->AddCampo('exercicio',    'char',    true,'4',true,true);
    $this->AddCampo('imagem',       'boolean', true,'', false,false);
    $this->AddCampo('anexo',        'text',    true,'', false,false);
}

function montaRecuperaRelacionamento()
{
    $stSql  = " SELECT                                                                          \n";
    $stSql .= "     SW_DOCUMENTO.COD_DOCUMENTO                                                  \n";
    $stSql .= "     ,SW_DOCUMENTO.NOM_DOCUMENTO                                                 \n";
    $stSql .= "     ,SW_DOCUMENTO_PROCESSO.COD_PROCESSO                                         \n";
    $stSql .= "     ,SW_DOCUMENTO_PROCESSO.EXERCICIO                                            \n";
    $stSql .= "     ,SW_COPIA_DIGITAL.COD_COPIA                                                 \n";
    $stSql .= "     ,SW_COPIA_DIGITAL.IMAGEM                                                    \n";
    $stSql .= "     ,SW_COPIA_DIGITAL.ANEXO                                                     \n";
    $stSql .= " FROM                                                                            \n";
    $stSql .= "     SW_DOCUMENTO,                                                               \n";
    $stSql .= "     SW_DOCUMENTO_PROCESSO,                                                      \n";
    $stSql .= "     SW_COPIA_DIGITAL                                                            \n";
    $stSql .= " WHERE                                                                           \n";
    $stSql .= "     SW_DOCUMENTO.COD_DOCUMENTO = SW_DOCUMENTO_PROCESSO.COD_DOCUMENTO            \n";
    $stSql .= "     AND SW_DOCUMENTO_PROCESSO.COD_DOCUMENTO = SW_COPIA_DIGITAL.COD_DOCUMENTO    \n";
    $stSql .= "     AND SW_DOCUMENTO_PROCESSO.COD_PROCESSO  = SW_COPIA_DIGITAL.COD_PROCESSO     \n";
    $stSql .= "     AND SW_DOCUMENTO_PROCESSO.EXERCICIO     = SW_COPIA_DIGITAL.EXERCICIO        \n";
    $stSql .= "     AND SW_DOCUMENTO_PROCESSO.COD_DOCUMENTO = ".$this->getDado('cod_documento')."\n";
    $stSql .= "     AND SW_DOCUMENTO_PROCESSO.COD_PROCESSO  = ".$this->getDado('cod_processo')."\n";
    $stSql .= "     AND SW_DOCUMENTO_PROCESSO.EXERCICIO     = '".$this->getDado('exercicio')."' \n";

    return $stSql;
}

}
