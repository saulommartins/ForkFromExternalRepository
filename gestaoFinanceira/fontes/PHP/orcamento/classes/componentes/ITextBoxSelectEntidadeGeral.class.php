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
    * Arquivo de textbox e select entidade geral
    * Data de Criação: 22/06/2006

    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Jose Eduardo Porto

    * @package URBEM
    * @subpackage

    $Revision: 30824 $
    $Name$
    $Author: hboaventura $
    $Date: 2006-12-01 10:29:11 -0200 (Sex, 01 Dez 2006) $

    * Casos de uso: uc-02.01.02
*/

include_once CLA_TEXTBOX_SELECT;

class ITextBoxSelectEntidadeGeral extends TextBoxSelect
{
    
public function __construct()
{
    parent::TextBoxSelect();

    $this->setRotulo              ( "Entidade"              );
    $this->setName                ( "inCodEntidade"      );
    $this->setTitle               ( "Selecione a entidade." );

    $this->obTextBox->setName        ( "inCodEntidade"             );
    $this->obTextBox->setId          ( "inCodEntidade"             );

    $this->obTextBox->setRotulo      ( "Entidade"                     );
    $this->obTextBox->setTitle       ( "Selecione a Entidade"         );
    $this->obTextBox->setInteiro     ( true                           );
    $this->obTextBox->setNull        ( false                          );

    $this->obSelect->setName          ( "stNomEntidade"               );
    $this->obSelect->setId            ( "stNomEntidade"               );
    $this->obSelect->setCampoId       ( "cod_entidade"                 );
    $this->obSelect->setCampoDesc     ( "nom_cgm"                      );
    $this->obSelect->setStyle         ( "width: 520"                   );
    $this->obSelect->setNull          ( false                          );
}

public function setExercicio($inValor)
{
    $this->inExercicio = $inValor;
}

public function setCodEntidade($inValor)
{
    $this->inCodEntidade = $inValor;
}

public function montaHTML()
{
    $this->obTextBox->setValue        ( $this->inCodEntidade );
    $this->obSelect->setValue         ( $this->inCodEntidade );
    $rsEntidadesGeral = new RecordSet;
    include_once ( TORC."TOrcamentoEntidade.class.php"    );
    $obTEntidade = new TOrcamentoEntidade();
    if ($this->inExercicio) {
        $obTEntidade->setDado('exercicio', $this->inExercicio);
    } else {
        $obTEntidade->setDado('exercicio', Sessao::getExercicio());
    }
    $obTEntidade->recuperaEntidadeGeral( $rsEntidadesGeral  );

    if ($rsEntidadesGeral->getNumLinhas()==1) {
        $this->obTextBox->setValue       ( $rsEntidadesGeral->getCampo('cod_entidade')  );
    }
    if ($rsEntidadesGeral->getNumLinhas()>1) {
        $this->obSelect->addOption              ( "", "Selecione"      );
    }
    $this->obSelect->preencheCombo    ( $rsEntidadesGeral              );
    parent::montaHTML();
}
}

?>