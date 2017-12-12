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
* Classe de mapeamento para administracao.setor
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3476 $
$Name$
$Author: pablo $
$Date: 2005-12-06 13:51:37 -0200 (Ter, 06 Dez 2005) $

Casos de uso: uc-01.03.97
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
    * Efetua conexão com tabela de Setor
    * Data de Criação   : 24/03/2004
    * @author Alessandro La-Rocca Silveira
*/
class TSetor extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TSetor()
{
    parent::Persistente();
    $this->setTabela('administracao.setor');
    $this->setComplementoChave('cod_orgao, cod_unidade, cod_departamento, cod_setor');

    $this->AddCampo('cod_orgao',           'integer',   true,  '', true,  true  );
    $this->AddCampo('cod_unidade',         'integer',   true,  '', true,  true  );
    $this->AddCampo('cod_departamento',    'integer',   true,  '', true,  true  );
    $this->AddCampo('cod_setor',           'integer',   true,  '', true,  false );
    $this->AddCampo('nom_setor',           'varchar',   true,  60, false, false );
    $this->AddCampo('ano_exercicio',       'char',      true,  '', false, false );
    $this->AddCampo('usuario_responsavel', 'integer',   false, '', false, true  );
}
}
