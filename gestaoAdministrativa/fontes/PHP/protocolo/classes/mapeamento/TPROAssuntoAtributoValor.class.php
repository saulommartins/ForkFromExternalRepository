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
* Classe de Mapeamento para a tabela sw_assunto_atributo
* Data de Criação: 05/09/2006

* @author Analista: Cassiano de Vasconcellos Ferreira
* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

$Revision: 15582 $
$Name$
$Author: cassiano $
$Date: 2006-09-18 08:38:09 -0300 (Seg, 18 Set 2006) $

Casos de uso: uc-01.06.93,  uc-01.06.98
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPROAssuntoAtributoValor extends Persistente
{
function TPROAssuntoAtributoValor()
{
    parent::Persistente();
    $this->setTabela('sw_assunto_atributo_valor');
    $this->setComplementoChave('cod_atributo,cod_assunto,cod_classificacao,cod_processo,exercicio');

    $this->AddCampo('cod_atributo',		'integer',true,	'',true,'TPROAtributoProtocolo');
    $this->AddCampo('cod_assunto',		'integer',true,	'',true,'TPROAssunto');
    $this->AddCampo('cod_classificacao','integer',true,	'',true,'TPROAssunto');
    $this->AddCampo('cod_processo',		'integer',true,	'',true,''); //SETAR A TPROProcesso quando for implementada
    $this->AddCampo('exercicio',		'char',true,   '4',true,'');//SETAR A TPROProcesso quando for implementada
    $this->AddCampo('valor',			'text',true,	'',false);
}

}
