/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos (urbem@cnm.org.br)      *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo  sob *
    * os termos da Licença Pública Geral GNU conforme publicada pela  Free  Software *
    * Foundation; tanto a versão 2 da Licença, como (a seu critério) qualquer versão *
    * posterior.                                                                     *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral  do  GNU  junto  com *
    * este programa; se não, escreva para  a  Free  Software  Foundation,  Inc.,  no *
    * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.               *
    *                                                                                *
    **********************************************************************************
*/
/*
* Script de DDL e DML
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
$Id:$
*
* Versão 2.00.0
*/


CREATE TABLE Requisicao (
  cod_almoxarifado INTEGER   NOT NULL ,
  cod_requisicao INTEGER   NOT NULL ,
  exercicio INTEGER   NOT NULL   ,
PRIMARY KEY(cod_almoxarifado, cod_requisicao, exercicio));




CREATE TABLE especialidade (
  cod_especialidade INTEGER   NOT NULL ,
  descricao VARCHAR      ,
PRIMARY KEY(cod_especialidade));




CREATE TABLE pessoal.servidor (
  numcgm INTEGER   NOT NULL ,
  cod_contrato INTEGER      ,
PRIMARY KEY(numcgm));




CREATE TABLE cid10 (
  cid10 VARCHAR   NOT NULL ,
  descricao_resumida VARCHAR    ,
  descricao_completa TEXT      ,
PRIMARY KEY(cid10));




CREATE TABLE sw_cgm_pessoa_juridica (
  numcgm INTEGER   NOT NULL   ,
PRIMARY KEY(numcgm));




CREATE TABLE procedimento (
  id_procedimento INTEGER   NOT NULL ,
  descricao INTEGER    ,
  ativo BOOL      ,
PRIMARY KEY(id_procedimento));




CREATE TABLE profissional (
  numcgm INTEGER   NOT NULL ,
  realiza_consulta BOOL    ,
  realiza_procedimento BOOL    ,
  ativo BOOL      ,
PRIMARY KEY(numcgm)  ,
  FOREIGN KEY(numcgm)
    REFERENCES pessoal.servidor(numcgm));


CREATE INDEX profissional_FKIndex1 ON profissional (numcgm);



CREATE INDEX IFK_Rel_06 ON profissional (numcgm);


CREATE TABLE pessoal.contrato (
  cod_contrato INTEGER   NOT NULL ,
  numcgm INTEGER   NOT NULL   ,
PRIMARY KEY(cod_contrato)  ,
  FOREIGN KEY(numcgm)
    REFERENCES pessoal.servidor(numcgm));


CREATE INDEX pessoal.contrato_FKIndex1 ON pessoal.contrato (numcgm);


CREATE INDEX IFK_Rel_35 ON pessoal.contrato (numcgm);


CREATE TABLE estabelecimento_saude (
  numcgm INTEGER   NOT NULL ,
  cnes INTEGER    ,
  ibge INTEGER    ,
  anvisa INTEGER    ,
  sia INTEGER    ,
  ativo BOOL      ,
PRIMARY KEY(numcgm)  ,
  FOREIGN KEY(numcgm)
    REFERENCES sw_cgm_pessoa_juridica(numcgm));


CREATE INDEX estabelecimento_saude_FKIndex1 ON estabelecimento_saude (numcgm);


CREATE INDEX IFK_Rel_21 ON estabelecimento_saude (numcgm);


-- ------------------------------------------------------------
-- Criar UK com cgm_estabelecimento_saude e dia_semana
-- ------------------------------------------------------------

CREATE TABLE horario_atendimento (
  id_horario_atendimento INTEGER   NOT NULL ,
  numcgm_estabelecimento_saude INTEGER   NOT NULL ,
  dia_semana INTEGER   NOT NULL ,
  hora_inicio TIME    ,
  hora_fim TIME      ,
PRIMARY KEY(id_horario_atendimento)  ,
  FOREIGN KEY(numcgm_estabelecimento_saude)
    REFERENCES estabelecimento_saude(numcgm));


CREATE INDEX horario_atendimento_FKIndex1 ON horario_atendimento (numcgm_estabelecimento_saude);


CREATE INDEX IFK_Rel_28 ON horario_atendimento (numcgm_estabelecimento_saude);


CREATE TABLE sw_cgm_pessoa_fisica (
  numcgm INTEGER   NOT NULL   ,
PRIMARY KEY(numcgm)  ,
  FOREIGN KEY(numcgm)
    REFERENCES pessoal.servidor(numcgm));


CREATE INDEX sw_cgm_pessoa_fisica_FKIndex1 ON sw_cgm_pessoa_fisica (numcgm);


CREATE INDEX IFK_Rel_07 ON sw_cgm_pessoa_fisica (numcgm);


CREATE TABLE profissional_estabelecimento_saude (
  id_estabelecimento_saude INTEGER   NOT NULL ,
  numcgm_profissional INTEGER   NOT NULL ,
  numcgm_estabelecimento_saude INTEGER   NOT NULL ,
  ativo BOOL      ,
PRIMARY KEY(id_estabelecimento_saude)    ,
  FOREIGN KEY(numcgm_profissional)
    REFERENCES profissional(numcgm),
  FOREIGN KEY(numcgm_estabelecimento_saude)
    REFERENCES estabelecimento_saude(numcgm));


CREATE INDEX profissional_has_estabelecimento_saude_FKIndex1 ON profissional_estabelecimento_saude (numcgm_profissional);
CREATE INDEX profissional_has_estabelecimento_saude_FKIndex2 ON profissional_estabelecimento_saude (numcgm_estabelecimento_saude);


CREATE INDEX IFK_Rel_41 ON profissional_estabelecimento_saude (numcgm_profissional);
CREATE INDEX IFK_Rel_42 ON profissional_estabelecimento_saude (numcgm_estabelecimento_saude);


CREATE TABLE profissional_procedimento (
  id_profissional_procedimento INTEGER   NOT NULL ,
  procedimento_id_procedimento INTEGER   NOT NULL ,
  numcgm_profissional INTEGER   NOT NULL ,
  ativo BOOL      ,
PRIMARY KEY(id_profissional_procedimento)  ,
  FOREIGN KEY(procedimento_id_procedimento)
    REFERENCES procedimento(id_procedimento),
  FOREIGN KEY(numcgm_profissional)
    REFERENCES profissional(numcgm));


CREATE INDEX profissional_procedimento_FKIndex2 ON profissional_procedimento (numcgm_profissional);


CREATE INDEX IFK_Rel_26 ON profissional_procedimento (procedimento_id_procedimento);
CREATE INDEX IFK_Rel_27 ON profissional_procedimento (numcgm_profissional);


CREATE TABLE pessoal.contrato_servidor_especialidade_cargo (
  cod_contrato INTEGER   NOT NULL ,
  cod_especialidade INTEGER   NOT NULL   ,
PRIMARY KEY(cod_contrato, cod_especialidade)    ,
  FOREIGN KEY(cod_contrato)
    REFERENCES pessoal.contrato(cod_contrato),
  FOREIGN KEY(cod_especialidade)
    REFERENCES especialidade(cod_especialidade));


CREATE INDEX pessoal.contrato_has_especialidade_FKIndex1 ON pessoal.contrato_servidor_especialidade_cargo (cod_contrato);
CREATE INDEX pessoal.contrato_has_especialidade_FKIndex2 ON pessoal.contrato_servidor_especialidade_cargo (cod_especialidade);


CREATE INDEX IFK_Rel_36 ON pessoal.contrato_servidor_especialidade_cargo (cod_contrato);
CREATE INDEX IFK_Rel_37 ON pessoal.contrato_servidor_especialidade_cargo (cod_especialidade);


CREATE TABLE paciente (
  numcgm INTEGER   NOT NULL ,
  cns INTEGER    ,
  tipo_sanguineo INTEGER    ,
  fator_rh CHAR    ,
  doador_sangue BOOL    ,
  doador_orgaos BOOL    ,
  alergico BOOL    ,
  frequenta_escola BOOL    ,
  observacao TEXT      ,
PRIMARY KEY(numcgm)  ,
  FOREIGN KEY(numcgm)
    REFERENCES sw_cgm_pessoa_fisica(numcgm));


CREATE INDEX paciente_FKIndex1 ON paciente (numcgm);



CREATE INDEX IFK_sw_cgm_pessoa_fisica ON paciente (numcgm);


CREATE TABLE paciente_mae (
  numcgm INTEGER   NOT NULL ,
  numcgm_mae INTEGER   NOT NULL   ,
PRIMARY KEY(numcgm)    ,
  FOREIGN KEY(numcgm)
    REFERENCES paciente(numcgm),
  FOREIGN KEY(numcgm_mae)
    REFERENCES sw_cgm_pessoa_fisica(numcgm));


CREATE INDEX paciente_mae_FKIndex2 ON paciente_mae (numcgm_mae);
CREATE INDEX paciente_mae_FKIndex2 ON paciente_mae (numcgm);


CREATE INDEX IFK_Rel_32 ON paciente_mae (numcgm);
CREATE INDEX IFK_Rel_34 ON paciente_mae (numcgm_mae);


CREATE TABLE paciente_pai (
  numcgm INTEGER   NOT NULL ,
  numcgm_pai INTEGER   NOT NULL   ,
PRIMARY KEY(numcgm)    ,
  FOREIGN KEY(numcgm_pai)
    REFERENCES sw_cgm_pessoa_fisica(numcgm),
  FOREIGN KEY(numcgm)
    REFERENCES paciente(numcgm));


CREATE INDEX paciente_pai_FKIndex2 ON paciente_pai (numcgm_pai);
CREATE INDEX paciente_pai_FKIndex2 ON paciente_pai (numcgm);


CREATE INDEX IFK_Rel_30 ON paciente_pai (numcgm_pai);
CREATE INDEX IFK_Rel_36 ON paciente_pai (numcgm);


CREATE TABLE consulta (
  id_consulta INTEGER   NOT NULL ,
  numcgm_paciente INTEGER   NOT NULL ,
  numcgm_atendente INTEGER   NOT NULL ,
  numcgm_profissional INTEGER   NOT NULL ,
  numcgm_estabelecimento_saude INTEGER   NOT NULL ,
  timestamp TIMESTAMP    ,
  dt_agendamento DATE    ,
  hora_agendamento TIME    ,
  dt_atendimento DATE    ,
  hora_atendimento TIME    ,
  motivo TEXT    ,
  diagnostico TEXT    ,
  prescricao TEXT      ,
PRIMARY KEY(id_consulta)        ,
  FOREIGN KEY(numcgm_paciente)
    REFERENCES paciente(numcgm),
  FOREIGN KEY(numcgm_estabelecimento_saude)
    REFERENCES estabelecimento_saude(numcgm),
  FOREIGN KEY(numcgm_profissional)
    REFERENCES profissional(numcgm),
  FOREIGN KEY(numcgm_atendente)
    REFERENCES profissional(numcgm));


CREATE INDEX consulta_FKIndex1 ON consulta (numcgm_paciente);
CREATE INDEX consulta_FKIndex2 ON consulta (numcgm_estabelecimento_saude);
CREATE INDEX consulta_FKIndex3 ON consulta (numcgm_profissional);
CREATE INDEX consulta_FKIndex4 ON consulta (numcgm_atendente);


CREATE INDEX IFK_Rel_14 ON consulta (numcgm_paciente);
CREATE INDEX IFK_Rel_22 ON consulta (numcgm_estabelecimento_saude);
CREATE INDEX IFK_Rel_65 ON consulta (numcgm_profissional);
CREATE INDEX IFK_Rel_66 ON consulta (numcgm_atendente);


CREATE TABLE procedimento_consulta (
  id_procedimento_consulta INTEGER   NOT NULL ,
  consulta_id_consulta INTEGER   NOT NULL ,
  profissional_procedimento_id_profissional_procedimento INTEGER   NOT NULL ,
  dt_procedimento DATE   NOT NULL ,
  hora_procedimento TIME   NOT NULL ,
  observacao TEXT   NOT NULL   ,
PRIMARY KEY(id_procedimento_consulta),
  FOREIGN KEY(consulta_id_consulta)
    REFERENCES consulta(id_consulta),
  FOREIGN KEY(profissional_procedimento_id_profissional_procedimento)
    REFERENCES profissional_procedimento(id_profissional_procedimento));


CREATE INDEX IFK_Rel_24 ON procedimento_consulta (consulta_id_consulta);
CREATE INDEX IFK_Rel_25 ON procedimento_consulta (profissional_procedimento_id_profissional_procedimento);


CREATE TABLE consulta_cid10 (
  id_consulta_cid10 INT   NOT NULL ,
  consulta_id_consulta INTEGER   NOT NULL ,
  cid10 VARCHAR   NOT NULL   ,
PRIMARY KEY(id_consulta_cid10)  ,
  FOREIGN KEY(consulta_id_consulta)
    REFERENCES consulta(id_consulta),
  FOREIGN KEY(cid10)
    REFERENCES cid10(cid10));


CREATE INDEX consulta_cid_FKIndex2 ON consulta_cid10 (cid10);


CREATE INDEX IFK_Rel_37 ON consulta_cid10 (consulta_id_consulta);
CREATE INDEX IFK_Rel_38 ON consulta_cid10 (cid10);


CREATE TABLE consulta_requisicao (
  id_consulta_requisicao INTEGER   NOT NULL ,
  consulta_id_consulta INTEGER   NOT NULL ,
  Requisicao_exercicio INTEGER   NOT NULL ,
  Requisicao_cod_requisicao INTEGER   NOT NULL ,
  Requisicao_cod_almoxarifado INTEGER   NOT NULL   ,
PRIMARY KEY(id_consulta_requisicao)    ,
  FOREIGN KEY(consulta_id_consulta)
    REFERENCES consulta(id_consulta),
  FOREIGN KEY(Requisicao_cod_almoxarifado, Requisicao_cod_requisicao, Requisicao_exercicio)
    REFERENCES Requisicao(cod_almoxarifado, cod_requisicao, exercicio));


CREATE INDEX consulta_requisicao_FKIndex1 ON consulta_requisicao (consulta_id_consulta);
CREATE INDEX consulta_requisicao_FKIndex2 ON consulta_requisicao (Requisicao_cod_almoxarifado, Requisicao_cod_requisicao, Requisicao_exercicio);


CREATE INDEX IFK_Rel_28 ON consulta_requisicao (consulta_id_consulta);
CREATE INDEX IFK_Rel_29 ON consulta_requisicao (Requisicao_cod_almoxarifado, Requisicao_cod_requisicao, Requisicao_exercicio);


CREATE TABLE historico_clinico (
  id_historico_clinico INTEGER   NOT NULL ,
  consulta_id_consulta INTEGER   NOT NULL ,
  numcgm_paciente INTEGER   NOT NULL ,
  numcgm_profissional INTEGER   NOT NULL ,
  dt_historico DATE   NOT NULL ,
  hora_historico TIME   NOT NULL ,
  pressao FLOAT    ,
  peso FLOAT    ,
  temperatura FLOAT    ,
  hgt INTEGER      ,
PRIMARY KEY(id_historico_clinico)    ,
  FOREIGN KEY(numcgm_profissional)
    REFERENCES profissional(numcgm),
  FOREIGN KEY(numcgm_paciente)
    REFERENCES paciente(numcgm),
  FOREIGN KEY(consulta_id_consulta)
    REFERENCES consulta(id_consulta));


CREATE INDEX historico_clinico_FKIndex2 ON historico_clinico (numcgm_profissional);
CREATE INDEX historico_clinico_FKIndex3 ON historico_clinico (numcgm_paciente);


CREATE INDEX IFK_Rel_64 ON historico_clinico (numcgm_profissional);
CREATE INDEX IFK_Rel_22 ON historico_clinico (numcgm_paciente);
CREATE INDEX IFK_Rel_23 ON historico_clinico (consulta_id_consulta);



