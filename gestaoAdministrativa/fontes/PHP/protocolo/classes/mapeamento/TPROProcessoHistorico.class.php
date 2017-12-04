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
* Classe de Mapeamento para a tabela processo_historico
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 17342 $
$Name$
$Author: cassiano $
$Date: 2006-10-31 14:19:59 -0300 (Ter, 31 Out 2006) $

Casos de uso: uc-01.06.98
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPROProcessoHistorico extends Persistente
{
function TPROProcessoHistorico()
{
    parent::Persistente();
    $this->setTabela('protocolo.processo_historico');
    $this->setComplementoChave('cod_processo,ano_exercicio');

    $this->AddCampo('cod_processo',     'integer',      true,   '',   true,  true );
    $this->AddCampo('ano_exercicio',    'varchar',      true,   '',   true,  true );
    $this->AddCampo('timestamp',        'timestamp',    false,  '',   true,  false );
    $this->AddCampo('cod_classificacao','integer',      true,   '',   false, true  );
    $this->AddCampo('cod_assunto',      'integer',      true,   '',   false, true  );
    $this->AddCampo('observacoes',      'text',         true,   '',   false, false );
    $this->AddCampo('resumo_assunto',   'varchar',      true,  '80', false, false );
}
}
