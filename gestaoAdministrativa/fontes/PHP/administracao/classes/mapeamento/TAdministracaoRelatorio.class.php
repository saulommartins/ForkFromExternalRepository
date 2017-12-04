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
* Classe de mapeamento para administracao.modulo
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 17754 $
$Name$
$Author: cassiano $
$Date: 2006-11-16 14:29:57 -0200 (Qui, 16 Nov 2006) $

Casos de uso: uc-01.01.00
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TAdministracaoRelatorio extends Persistente
{
    public function TAdministracaoRelatorio()
    {
        parent::Persistente();
        $this->setTabela('administracao.relatorio');
        $this->setCampoCod('cod_relatorio');
        $this->setComplementoChave('cod_gestao,cod_modulo');

        $this->AddCampo('cod_modulo',   'integer', true, '', true,  true  );
        $this->AddCampo('cod_gestao',   'integer', true, '', false, true   );
        $this->AddCampo('cod_relatorio','integer', true, '', false, true   );
        $this->AddCampo('nom_relatorio','varchar', true, 80, false, false  );
        $this->AddCampo('arquivo',      'varchar', true, 30, false, false  );
    }

    public function montaRecuperaRelacionamento()
    {
        $stSql  = " SELECT                                                                  \n";
        $stSql .= "      administracao.relatorio.cod_gestao                                 \n";
        $stSql .= "      ,administracao.relatorio.cod_modulo                                \n";
        $stSql .= "      ,administracao.relatorio.cod_relatorio                             \n";
        $stSql .= "      ,administracao.relatorio.nom_relatorio                             \n";
        $stSql .= "      ,administracao.relatorio.arquivo                                   \n";
        $stSql .= "  FROM                                                                   \n";
        $stSql .= "      administracao.relatorio,                                           \n";
        $stSql .= "      ".$this->getDado('mapeamento')." as tabela                          \n";
        $stSql .= "  WHERE                                                                  \n";
        $stSql .= "      administracao.relatorio.cod_gestao        = tabela.cod_gestao      \n";
        $stSql .= "      AND administracao.relatorio.cod_modulo    = tabela.cod_modulo      \n";
        $stSql .= "      AND administracao.relatorio.cod_relatorio = tabela.cod_relatorio   \n";

        return $stSql;
    }
}
