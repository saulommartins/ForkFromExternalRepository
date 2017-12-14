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
* Data de Criação: 01/09/2006

* @author Analista: Cassiano de Vasconcellos Ferreira
* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

$Revision: 16316 $
$Name$
$Author: cassiano $
$Date: 2006-10-03 13:10:47 -0300 (Ter, 03 Out 2006) $

Casos de uso: uc-01.06.97
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPROAndamentoPadrao extends Persistente
{
function TPROAndamentoPadrao()
{
    parent::Persistente();
    $this->setTabela('sw_andamento_padrao');
    $this->setComplementoChave('num_passagens, cod_classificacao, cod_assunto, cod_orgao, cod_unidade, cod_departamento, cod_setor, ano_exercicio');

    $this->AddCampo('num_passagens',	'integer',true,	'',true,false);
    $this->AddCampo('cod_assunto',		'integer',true,	'',true,'TPROAssunto');
    $this->AddCampo('cod_classificacao','integer',true,	'',true,'TPROAssunto');
    $this->AddCampo('cod_orgao',        'integer',true,	'',true,true);
    $this->AddCampo('cod_unidade',      'integer',true,	'',true,true);
    $this->AddCampo('cod_departamento', 'integer',true,	'',true,true);
    $this->AddCampo('cod_setor',        'integer',true,	'',true,true);
    $this->AddCampo('ano_exercicio',    'char',true,	'4',true,true);
    $this->AddCampo('descricao',        'text',true,	'',false,false);
    $this->AddCampo('num_dia',          'integer',true,	'',false,false);
    $this->AddCampo('ordem',            'integer',true,	'',false,false);
}

}
